<?php

namespace App\Support;

class ProgramCodeResolver
{
    public static function resolve(?string $course): string
    {
        if (empty($course)) {
            return 'BSIT';
        }

        $exactMatches = [
            'Bachelor of Science in Information Technology (BSIT)' => 'BSIT',
            'Bachelor of Elementary Education (BEED)' => 'BEED',
            'Bachelor of Secondary Education (BSEd) major in Mathematics' => 'BSEd-Math',
            'Bachelor of Secondary Education (BSEd) major in Science' => 'BSEd-Science',
            'Bachelor of Physical Education (BPEd)' => 'BPEd',
            'Bachelor of Technical-Vocational Teacher Education (BTVTEd)' => 'BTVTEd',
            'Diploma in Teaching Secondary (DTS)' => 'DTS',
            'Bachelor of Science in Hospitality Management (BSHM)' => 'BSHM',
            'Bachelor of Science in Civil Engineering (BSCE)' => 'BSCE',
            'Bachelor of Science in Electrical Engineering (BSEE)' => 'BSEE',
            'Bachelor of Science in Mechanical Engineering (BSME)' => 'BSME',
            'Bachelor of Industrial Technology (BIT) major in Culinary Arts (CA)' => 'BIT-CA',
            'Bachelor of Industrial Technology (BIT) major in Electronics (ET)' => 'BIT-ET',
        ];

        foreach ($exactMatches as $programName => $code) {
            if (strcasecmp(trim($course), $programName) === 0) {
                return $code;
            }
        }

        $courseLower = strtolower(trim($course));

        $codeMatches = [
            '(bsit)' => 'BSIT',
            '(beed)' => 'BEED',
            '(bsed) major in mathematics' => 'BSEd-Math',
            '(bsed) major in science' => 'BSEd-Science',
            '(bped)' => 'BPEd',
            '(btvted)' => 'BTVTEd',
            '(dts)' => 'DTS',
            '(bshm)' => 'BSHM',
            '(bsce)' => 'BSCE',
            '(bsee)' => 'BSEE',
            '(bsme)' => 'BSME',
            '(bit) major in culinary arts (ca)' => 'BIT-CA',
            '(bit) major in electronics (et)' => 'BIT-ET',
        ];

        foreach ($codeMatches as $pattern => $code) {
            if (str_contains($courseLower, $pattern)) {
                return $code;
            }
        }

        $keywordMatches = [
            'information technology' => 'BSIT',
            'elementary education' => 'BEED',
            'mathematics' => 'BSEd-Math',
            'science' => 'BSEd-Science',
            'physical education' => 'BPEd',
            'technical-vocational' => 'BTVTEd',
            'teaching secondary' => 'DTS',
            'hospitality management' => 'BSHM',
            'civil engineering' => 'BSCE',
            'electrical engineering' => 'BSEE',
            'mechanical engineering' => 'BSME',
            'culinary arts' => 'BIT-CA',
            'electronics' => 'BIT-ET',
        ];

        foreach ($keywordMatches as $keyword => $code) {
            if (str_contains($courseLower, $keyword)) {
                return $code;
            }
        }

        return 'BSIT';
    }

    public static function yearLevels(): array
    {
        return config('program_sections.year_levels', []);
    }

    public static function sectionsForCourse(?string $course): array
    {
        $code = static::resolve($course);
        $sections = config('program_sections.sections', []);

        return $sections[$code] ?? ($sections['default'] ?? ['A', 'B', 'C']);
    }

    public static function buildCourseSectionCode(?string $course, ?string $yearLevel, ?string $section): ?string
    {
        if (!$course || !$yearLevel) {
            return null;
        }

        $code = static::resolve($course);
        $sectionPart = strtoupper(trim((string) $section));

        if ($sectionPart !== '') {
            return sprintf('%s-%s%s', $code, $yearLevel, $sectionPart);
        }

        return sprintf('%s-%s', $code, $yearLevel);
    }
}



