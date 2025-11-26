<?php

namespace App\Services;

use App\Models\AcceptanceLetter;
use App\Models\MonthlyEvaluation;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class MonthlyEvaluationPdfService
{
    public function generate(MonthlyEvaluation $evaluation): string
    {
        $templatePath = resource_path('templates/monthlyprogressevaulationformtemplate(final).pdf');

        if (! file_exists($templatePath)) {
            throw new \Exception('Monthly evaluation template not found');
        }

        $pdf = new Fpdi();
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        // Let template determine page size automatically
        $pdf->AddPage();
        $pdf->setSourceFile($templatePath);
        $template = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($template);
        $pdf->useTemplate($template, 0, 0, $size['width'], $size['height']);

        // Header fields based on exact coordinates
        $dateSource = $evaluation->submitted_at ?? $evaluation->created_at ?? Carbon::now();
        $dateText = Carbon::parse($dateSource)->format('F d, Y');
        $monthYearText = strtoupper($evaluation->getMonthName()).' '.$evaluation->evaluation_year;

        // Date (10pt, BOLD) - X: 6.15", Y: 2.34"
        $this->writeText($pdf, 6.15, 2.34, $dateText, 10, 'B');

        // Month & Year (10pt, BOLD) - X: 2.38", Y: 2.69"
        $this->writeText($pdf, 2.38, 2.69, $monthYearText, 10, 'B');

        // Student Name (9pt, BOLD) - X: 2.66", Y: 3.01"
        $this->writeText($pdf, 2.66, 3.01, $evaluation->student_name ?? '', 9, 'B');

        // HTE Name (9pt, BOLD) - X: 3.25", Y: 3.18"
        $this->writeText($pdf, 3.25, 3.18, $evaluation->hte_name ?? 'N/A', 9, 'B');

        // Address (9pt, BOLD) - X: 2.17", Y: 3.36"
        $this->writeText($pdf, 2.17, 3.36, $evaluation->hte_address ?? 'N/A', 9, 'B');

        // Work Assignment (9pt, BOLD) - X: 2.33", Y: 3.53"
        $this->writeText($pdf, 2.33, 3.53, $evaluation->work_assignment ?? 'N/A', 9, 'B');

        // Work Schedule (9pt, BOLD) - X: 2.11", Y: 3.67"
        $formattedSchedule = $this->formatWorkSchedule($evaluation);
        $this->writeText($pdf, 2.11, 3.67, $formattedSchedule, 9, 'B');

        // Supervisor Name (9pt, BOLD) - X: 2.88", Y: 3.83"
        $this->writeText($pdf, 2.88, 3.83, $evaluation->supervisor_name ?? '', 9, 'B');

        // Rating Check Marks (8pt)
        // Y coordinates for 20 rows
        $yCoords = [
            4.90, 5.05, 5.20, 5.35, 5.50,  // 1-5
            5.80, 5.95, 6.10, 6.25, 6.40,  // 6-10
            6.70, 6.85, 7.00, 7.15, 7.30,  // 11-15
            7.60, 7.75, 7.90, 8.05, 8.20,   // 16-20 (Row 16: 7.60, Row 17: 7.75, Row 18: 7.90, Row 19: 8.05, Row 20: 8.20)
        ];

        // X coordinates for ratings
        $xCoords = [
            5 => 4.45,  // Excellent
            4 => 5.11,  // Very Satisfactory
            3 => 5.86,  // Satisfactory
            2 => 6.50,  // Fair
            1 => 7.12,   // Needs Improvement
        ];

        // Place check marks (8pt) - using Wingdings font for checkmark symbol
        for ($i = 1; $i <= 20; $i++) {
            $rating = $evaluation->{"rating_row_$i"};
            if ($rating && isset($xCoords[$rating])) {
                $this->writeCheckmark($pdf, $xCoords[$rating], $yCoords[$i - 1], 8);
            }
        }

        // Comments (8pt, REGULAR - NOT BOLD, max 4 lines) - Starting X: 1.04", Y: 8.68"
        if ($evaluation->comments_recommendations) {
            $lines = $this->splitIntoLines($evaluation->comments_recommendations, 4, 100);
            $yPositions = [8.68, 8.80, 8.92, 9.04];

            foreach ($lines as $index => $line) {
                if ($index < 4 && $line) {
                    // Comments remain regular (not bold)
                    $this->writeText($pdf, 1.04, $yPositions[$index], $line, 8, '');
                }
            }
        }

        // Signatures (9pt, BOLD)
        // Supervisor Signature - X: 1.04", Y: 9.66"
        $this->writeText($pdf, 1.04, 9.66, $evaluation->supervisor_name ?? '', 9, 'B');

        // Student Signature - X: 4.99", Y: 9.66"
        $this->writeText($pdf, 4.99, 9.66, $evaluation->student_name ?? '', 9, 'B');

        // Signature Dates (9pt, BOLD)
        // Supervisor Date - X: 1.33", Y: 10.05"
        $supervisorDate = Carbon::parse($dateSource)->format('F d, Y');
        $this->writeText($pdf, 1.33, 10.05, $supervisorDate, 9, 'B');

        // Student Date - X: 5.35", Y: 10.05"
        $studentDate = Carbon::parse($dateSource)->format('F d, Y');
        $this->writeText($pdf, 5.35, 10.05, $studentDate, 9, 'B');

        return $pdf->Output('S');
    }

    private function writeText(Fpdi $pdf, float $xInches, float $yInches, string $text, int $fontSize, string $style = ''): void
    {
        $pdf->SetFont('Helvetica', $style, $fontSize);
        // Convert inches to millimeters (multiply by 25.4)
        // Add 3mm offset for baseline alignment (same as WeeklyReportPdfService)
        $x = $xInches * 25.4;
        $y = ($yInches * 25.4) + 3;
        $pdf->Text($x, $y, $text);
    }

    private function writeCheckmark(Fpdi $pdf, float $xInches, float $yInches, int $fontSize): void
    {
        // Use ZapfDingbats font which has a checkmark symbol
        // Character code 52 (decimal) in ZapfDingbats is a checkmark (✓)
        $pdf->SetFont('ZapfDingbats', '', $fontSize);
        $checkmark = chr(52); // In ZapfDingbats, character 52 is a checkmark symbol

        // Convert inches to millimeters (multiply by 25.4)
        // Add 3mm offset for baseline alignment
        $x = $xInches * 25.4;
        $y = ($yInches * 25.4) + 3;
        $pdf->Text($x, $y, $checkmark);
    }

    private function formatWorkSchedule(MonthlyEvaluation $evaluation): string
    {
        // Try to get the acceptance letter to access raw schedule data
        $student = $evaluation->student;
        if (! $student) {
            return $evaluation->work_schedule ?? 'N/A';
        }

        $acceptance = AcceptanceLetter::where('student_user_id', $student->id)
            ->latest('start_date')
            ->first();

        if (! $acceptance || ! $acceptance->work_schedule) {
            return $evaluation->work_schedule ?? 'N/A';
        }

        $schedule = is_string($acceptance->work_schedule)
            ? json_decode($acceptance->work_schedule, true)
            : $acceptance->work_schedule;

        if (! is_array($schedule)) {
            return $evaluation->work_schedule ?? 'N/A';
        }

        // Map full day names to 3-letter abbreviations
        $dayAbbreviations = [
            'monday' => 'Mon',
            'tuesday' => 'Tue',
            'wednesday' => 'Wed',
            'thursday' => 'Thu',
            'friday' => 'Fri',
            'saturday' => 'Sat',
            'sunday' => 'Sun',
        ];

        // Extract working days
        $workingDays = [];
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            if (isset($schedule[$day]['enabled']) && $schedule[$day]['enabled']) {
                $workingDays[] = $dayAbbreviations[$day] ?? ucfirst(substr($day, 0, 3));
            }
        }

        if (empty($workingDays)) {
            return 'N/A';
        }

        // Get shift times (24-hour format)
        $shiftStart = $schedule['shift_start'] ?? '08:00';
        $shiftEnd = $schedule['shift_end'] ?? '17:00';

        // Format time - convert to 12-hour format (e.g., 8:00 AM to 5:00 PM)
        $formatTime = function ($time) {
            try {
                // Try H:i:s format first
                $parsed = Carbon::createFromFormat('H:i:s', $time);
            } catch (\Exception $e) {
                try {
                    // Try H:i format
                    $parsed = Carbon::createFromFormat('H:i', $time);
                } catch (\Exception $e) {
                    // Return as is if parsing fails
                    return $time;
                }
            }
            // Format as 12-hour with AM/PM (e.g., "8:00 AM", "5:00 PM")
            return $parsed->format('g:i A');
        };

        $startTime = $formatTime($shiftStart);
        $endTime = $formatTime($shiftEnd);

        // Format as: "Mon, Tue, Wed (8:00 AM to 5:00 PM)"
        $daysStr = implode(', ', $workingDays);

        return $daysStr.' ('.$startTime.' to '.$endTime.')';
    }

    private function splitIntoLines(string $text, int $maxLines, int $maxCharsPerLine): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            // Handle long words that exceed max chars per line
            if (strlen($word) > $maxCharsPerLine) {
                // Add current line if it has content
                if ($currentLine) {
                    $lines[] = $currentLine;
                    $currentLine = '';
                    if (count($lines) >= $maxLines) {
                        break;
                    }
                }

                // Break long word into chunks
                $chunks = str_split($word, $maxCharsPerLine);
                foreach ($chunks as $chunk) {
                    if (count($lines) >= $maxLines) {
                        break 2;
                    }
                    $lines[] = $chunk;
                }

                continue;
            }

            $testLine = $currentLine ? $currentLine.' '.$word : $word;

            if (strlen($testLine) > $maxCharsPerLine) {
                if ($currentLine) {
                    $lines[] = $currentLine;
                    $currentLine = $word;
                } else {
                    $lines[] = $word;
                    $currentLine = '';
                }

                if (count($lines) >= $maxLines) {
                    break;
                }
            } else {
                $currentLine = $testLine;
            }
        }

        if ($currentLine && count($lines) < $maxLines) {
            $lines[] = $currentLine;
        }

        return array_slice($lines, 0, $maxLines);
    }
}
