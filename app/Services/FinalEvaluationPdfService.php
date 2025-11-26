<?php

namespace App\Services;

use App\Models\FinalEvaluation;
use setasign\Fpdi\Fpdi;

class FinalEvaluationPdfService extends BasePdfService
{
    private const LEFT_MARGIN = 0.94;

    private const TOP_MARGIN = 0.47;

    private string $templatePath;

    public function __construct()
    {
        $this->templatePath = resource_path('templates/finalevaluation.pdf');
    }

    public function generate(FinalEvaluation $evaluation): string
    {
        if (! file_exists($this->templatePath)) {
            throw new \RuntimeException('Final evaluation template not found.');
        }

        $pdf = new Fpdi();
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);

        // Add Legal size page (8.5" x 14" = 215.9mm x 355.6mm)
        $pdf->AddPage('P', [215.9, 355.6]);

        $pdf->setSourceFile($this->templatePath);
        $template = $pdf->importPage(1);

        // Use template at full Legal size with no margins
        $pdf->useTemplate($template, 0, 0, 215.9, 355.6);

        // Date (11pt, BOLD) - X: 5.38", Y: 1.50"
        $dateText = $evaluation->submitted_at ? $evaluation->submitted_at->format('m/d/Y') : now()->format('m/d/Y');
        $this->writeText($pdf, 5.38, 1.50, $dateText, 11, 'B', self::LEFT_MARGIN, self::TOP_MARGIN);

        // Rating Section (7 criteria) - All X: 5.70"
        $ratings = [
            ['value' => $evaluation->rating_quality_thoroughness, 'y' => 3.05], // 1st - 20%
            ['value' => $evaluation->rating_dependability, 'y' => 4.09], // 2nd - 15%
            ['value' => $evaluation->rating_quality_completion, 'y' => 5.15], // 3rd - 20%
            ['value' => $evaluation->rating_attendance, 'y' => 6.10], // 4th - 15%
            ['value' => $evaluation->rating_cooperation, 'y' => 7.01], // 5th - 10%
            ['value' => $evaluation->rating_judgement, 'y' => 7.90], // 6th - 10%
            ['value' => $evaluation->rating_personality, 'y' => 8.80], // 7th - 5%
        ];

        foreach ($ratings as $rating) {
            if ($rating['value']) {
                $this->writeText($pdf, 5.70, $rating['y'], number_format($rating['value'], 2), 11, 'B', self::LEFT_MARGIN, self::TOP_MARGIN);
            }
        }

        // Total Rating - X: 5.70", Y: 9.51"
        $this->writeText($pdf, 5.70, 9.51, number_format($evaluation->total_rating, 2), 11, 'B', self::LEFT_MARGIN, self::TOP_MARGIN);

        // Comments Section (multi-line) - X: 0.10" for all lines
        if ($evaluation->comments_recommendations) {
            $this->writeWrappedLines($pdf, $evaluation->comments_recommendations, [
                ['x' => 0.10, 'y' => 10.20], // 1st line
                ['x' => 0.10, 'y' => 10.39], // 2nd line
                ['x' => 0.10, 'y' => 10.59], // 3rd line
                ['x' => 0.10, 'y' => 10.79], // 4th line
            ], 100, 10, '', self::LEFT_MARGIN, self::TOP_MARGIN);
        }

        // Signature of Manager/Supervisor - X: 0.06", Y: 11.51"
        $this->writeText($pdf, 0.06, 11.51, $evaluation->supervisor_name, 11, 'B', self::LEFT_MARGIN, self::TOP_MARGIN);

        // Signature of Student Trainee - X: 4.50", Y: 11.51"
        $this->writeText($pdf, 4.50, 11.51, $evaluation->student_name, 11, 'B', self::LEFT_MARGIN, self::TOP_MARGIN);

        // Date 1 (Supervisor) - X: 0.06", Y: 12.20"
        $supervisorDate = $evaluation->supervisor_signature_date ? $evaluation->supervisor_signature_date->format('m/d/Y') : $dateText;
        $this->writeText($pdf, 0.06, 12.20, $supervisorDate, 11, '', self::LEFT_MARGIN, self::TOP_MARGIN);

        // Date 2 (Student) - X: 4.50", Y: 12.20"
        $studentDate = $evaluation->student_signature_date ? $evaluation->student_signature_date->format('m/d/Y') : $dateText;
        $this->writeText($pdf, 4.50, 12.20, $studentDate, 11, '', self::LEFT_MARGIN, self::TOP_MARGIN);

        return $pdf->Output('S');
    }
}
