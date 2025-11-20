<?php

namespace App\Services;

use App\Models\AcceptanceLetter;
use App\Models\WeeklyReport;
use setasign\Fpdi\Fpdi;

class WeeklyReportPdfService
{
    private string $templatePath;

    public function __construct()
    {
        $this->templatePath = resource_path('templates/weekly-accomplishment.pdf');
    }

    public function generate(WeeklyReport $report): string
    {
        if (!file_exists($this->templatePath)) {
            throw new \RuntimeException('Weekly accomplishment template not found.');
        }

        $student = $report->student;
        $profile = $student->studentProfile;
        $acceptance = AcceptanceLetter::where('student_user_id', $student->id)
            ->latest('start_date')
            ->with('company')
            ->first();

        $pdf = new Fpdi();
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();
        $pdf->setSourceFile($this->templatePath);
        $template = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($template);
        $pdf->useTemplate($template, 0, 0, $size['width'], $size['height']);
        $pdf->SetFont('Helvetica', '', 11);

        // Student Name (BOLD)
        $this->writeText($pdf, 1.08, 1.93, $student->name, 11, 'B');

        // Course/Year (BOLD)
        $courseYear = $profile?->course_section_display ?? $profile?->course ?? '';
        $this->writeText($pdf, 6.50, 1.93, $courseYear, 11, 'B');

        // Company Name and Address (BOLD, single line, no wrapping unless overflow)
        $companyName = $acceptance?->company?->name ?? $profile?->company?->name ?? 'Not Assigned';
        $companyAddress = $acceptance?->company?->address ?? $profile?->company?->address ?? '';
        
        // Combine name and address on same line if address exists
        $companyInfo = $companyName;
        if ($companyAddress) {
            $companyInfo .= ', ' . $companyAddress;
        }
        
        // Write company info in BOLD
        $this->writeText($pdf, 0.47, 2.60, $companyInfo, 11, 'B');

        $entries = $this->normalizeEntries($report->entries ?? []);
        $rowPositions = [3.98, 4.21, 4.44, 4.67, 4.90, 5.13, 5.36, 5.59];
        $dateX = 0.71;
        $activityX = 2.11;
        $hoursX = 6.86;

        foreach ($rowPositions as $index => $yPosition) {
            $entry = $entries[$index] ?? null;
            if ($entry) {
                $this->writeText($pdf, $dateX, $yPosition, $entry['date']);
                $this->writeText($pdf, $activityX, $yPosition, $entry['activity']);
                $this->writeText($pdf, $hoursX, $yPosition, $entry['hours']);
            }
        }

        // Total Hours (BOLD)
        $this->writeText($pdf, 6.49, 6.13, number_format($report->total_hours, 2), 11, 'B');

        $problems = $report->problems_encountered ?? '';
        if ($problems) {
            $this->writeWrappedLines($pdf, $problems, [
                ['x' => 0.53, 'y' => 6.93],
                ['x' => 0.53, 'y' => 7.38],
                ['x' => 0.53, 'y' => 7.85],
            ]);
        }

        // Training Supervisor (BOLD)
        $supervisorName = $acceptance?->immediate_supervisor ?? '________________________';
        $this->writeText($pdf, 0.72, 9.52, $supervisorName, 11, 'B');

        // Student Signature (BOLD) - moved to x=4.95, y=9.50
        $studentSignature = $student->name;
        $this->writeText($pdf, 4.95, 9.50, $studentSignature, 11, 'B');

        return $pdf->Output('S');
    }

    private function writeText(Fpdi $pdf, float $xInches, float $yInches, string $text, int $fontSize = 11, string $style = ''): void
    {
        $pdf->SetFont('Helvetica', $style, $fontSize);
        $x = $xInches * 25.4; // Convert inches to mm
        $y = ($yInches * 25.4) + 3; // Convert inches to mm and add 3mm offset for baseline
        $pdf->Text($x, $y, $text);
    }

    private function writeWrappedLines(Fpdi $pdf, string $text, array $lines): void
    {
        $wrapped = preg_split('/\r\n|\r|\n/', wordwrap($text, 60, "\n"));
        foreach ($lines as $index => $coords) {
            if (!isset($wrapped[$index])) {
                break;
            }
            $this->writeText($pdf, $coords['x'], $coords['y'], $wrapped[$index]);
        }
    }

    private function normalizeEntries(array $entries): array
    {
        $normalized = [];
        foreach ($entries as $entry) {
            // Skip entries with no hours (absent days)
            $hours = $entry['hours'] ?? 0;
            if (empty($hours) || (float)$hours <= 0) {
                continue;
            }
            
            $normalized[] = [
                'date' => isset($entry['date']) ? date('M d, Y', strtotime($entry['date'])) : '',
                'activity' => $entry['activity'] ?? '',
                'hours' => $hours,
            ];
        }

        // Pad to 8 rows with empty entries (for template consistency)
        return array_pad($normalized, 8, ['date' => '', 'activity' => '', 'hours' => '']);
    }


}

