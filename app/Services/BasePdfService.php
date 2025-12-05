<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

abstract class BasePdfService
{
    /**
     * Write text to PDF at specified coordinates
     *
     * @param  Fpdi  $pdf PDF instance
     * @param  float  $xInches X coordinate in inches
     * @param  float  $yInches Y coordinate in inches
     * @param  string  $text Text to write
     * @param  int  $fontSize Font size
     * @param  string  $style Font style (B for bold, I for italic, etc.)
     * @param  float  $leftMargin Left margin in inches (default 0)
     * @param  float  $topMargin Top margin in inches (default 0)
     */
    protected function writeText(
        Fpdi $pdf,
        float $xInches,
        float $yInches,
        string $text,
        int $fontSize = 11,
        string $style = '',
        float $leftMargin = 0,
        float $topMargin = 0
    ): void {
        $pdf->SetFont('Helvetica', $style, $fontSize);

        // Add margins to coordinates
        $x = ($xInches + $leftMargin) * 25.4; // Convert inches to mm
        $y = (($yInches + $topMargin) * 25.4) + 3; // Convert inches to mm and add 3mm offset for baseline

        $pdf->Text($x, $y, utf8_decode($text));
    }

    /**
     * Write wrapped text lines to PDF
     *
     * @param  Fpdi  $pdf PDF instance
     * @param  string  $text Text to wrap
     * @param  array  $lines Array of coordinate arrays ['x' => float, 'y' => float]
     * @param  int  $wrapLength Characters per line
     * @param  int  $fontSize Font size
     * @param  string  $style Font style
     * @param  float  $leftMargin Left margin in inches
     * @param  float  $topMargin Top margin in inches
     */
    protected function writeWrappedLines(
        Fpdi $pdf,
        string $text,
        array $lines,
        int $wrapLength = 100,
        int $fontSize = 10,
        string $style = '',
        float $leftMargin = 0,
        float $topMargin = 0
    ): void {
        $wrapped = preg_split('/\r\n|\r|\n/', wordwrap($text, $wrapLength, "\n"));
        foreach ($lines as $index => $coords) {
            if (! isset($wrapped[$index])) {
                break;
            }
            $this->writeText(
                $pdf,
                $coords['x'],
                $coords['y'],
                $wrapped[$index],
                $fontSize,
                $style,
                $leftMargin,
                $topMargin
            );
        }
    }

    /**
     * Split text into lines
     *
     * @param  string  $text Text to split
     * @param  int  $maxLines Maximum number of lines
     * @param  int  $wrapLength Characters per line
     * @return array Array of text lines
     */
    protected function splitIntoLines(string $text, int $maxLines, int $wrapLength = 100): array
    {
        $wrapped = wordwrap($text, $wrapLength, "\n");
        $lines = preg_split('/\r\n|\r|\n/', $wrapped);

        return array_slice($lines, 0, $maxLines);
    }
}
