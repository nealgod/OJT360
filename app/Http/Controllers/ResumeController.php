<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403, 'Only students can access resume builder.');
        }

        $resumes = Resume::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        return view('resume.index', compact('resumes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403, 'Only students can create resumes.');
        }

        // Auto-fill from student profile
        $studentProfile = $user->studentProfile;
        $defaultData = [
            'personal_info' => [
                'name' => $user->name,
                'job_title' => '',
                'email' => $user->email,
                'phone' => $studentProfile->phone ?? '',
                'address' => '',
            ],
            'education' => [
                [
                    'institution' => 'Eastern Visayas State University',
                    'degree' => $studentProfile->course ?? '',
                    'department' => $studentProfile->department ?? '',
                    'year' => '',
                ]
            ],
        ];

        return view('resume.create', ['defaultData' => $defaultData]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            abort(403);
        }

        $validated = $request->validate([
            'personal_info' => 'required|array',
            'personal_info.name' => 'required|string|max:255',
            'personal_info.job_title' => 'nullable|string|max:255',
            'personal_info.email' => 'required|email|max:255',
            'personal_info.phone' => 'nullable|string|max:50',
            'personal_info.address' => 'nullable|string|max:500',
            'objective' => 'nullable|string|max:1000',
            'education' => 'nullable|array',
            'work_experience' => 'nullable|array',
            'skills' => 'nullable|array',
            'certifications' => 'nullable|array',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Personal info sanitization
        $personalInfo = $validated['personal_info'];
        $personalInfo['job_title'] = isset($personalInfo['job_title']) ? trim($personalInfo['job_title']) : null;
        $personalInfo['job_title'] = $personalInfo['job_title'] === '' ? null : $personalInfo['job_title'];
        $personalInfo['phone'] = isset($personalInfo['phone']) ? trim($personalInfo['phone']) : null;
        $personalInfo['address'] = isset($personalInfo['address']) ? trim($personalInfo['address']) : null;

        // Collections sanitization
        $education = collect($validated['education'] ?? [])->map(function ($edu) {
            return [
                'institution' => trim($edu['institution'] ?? ''),
                'degree' => trim($edu['degree'] ?? ''),
                'department' => trim($edu['department'] ?? ''),
                'year' => trim($edu['year'] ?? ''),
            ];
        })->filter(function ($edu) {
            return $edu['institution'] !== '' || $edu['degree'] !== '' || $edu['department'] !== '' || $edu['year'] !== '';
        })->values()->all();

        $workExperience = collect($validated['work_experience'] ?? [])->map(function ($exp) {
            return [
                'company' => trim($exp['company'] ?? ''),
                'position' => trim($exp['position'] ?? ''),
                'start_date' => trim($exp['start_date'] ?? ''),
                'end_date' => trim($exp['end_date'] ?? ''),
                'description' => trim($exp['description'] ?? ''),
            ];
        })->filter(function ($exp) {
            return $exp['company'] !== '' || $exp['position'] !== '' || $exp['start_date'] !== '' || $exp['end_date'] !== '' || $exp['description'] !== '';
        })->values()->all();

        $skills = collect($validated['skills'] ?? [])->map(fn ($skill) => trim($skill ?? ''))->filter()->values()->all();

        $certifications = collect($validated['certifications'] ?? [])->map(function ($cert) {
            $name = trim($cert['name'] ?? '');
            return $name ? ['name' => $name] : null;
        })->filter()->values()->all();

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('resume-images', 'public');
        }

        $resume = Resume::create([
            'user_id' => $user->id,
            'personal_info' => $personalInfo,
            'objective' => isset($validated['objective']) ? trim($validated['objective']) : null,
            'education' => $education,
            'work_experience' => $workExperience,
            'skills' => $skills,
            'certifications' => $certifications,
            'profile_image' => $profileImagePath,
        ]);

        return redirect()->route('resume.index')->with('success', 'Resume created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resume $resume)
    {
        $user = Auth::user();
        
        if ($resume->user_id !== $user->id) {
            abort(403);
        }

        return view('resume.edit', compact('resume'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resume $resume)
    {
        $user = Auth::user();
        
        if ($resume->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'personal_info' => 'required|array',
            'personal_info.name' => 'required|string|max:255',
            'personal_info.job_title' => 'nullable|string|max:255',
            'personal_info.email' => 'required|email|max:255',
            'personal_info.phone' => 'nullable|string|max:50',
            'personal_info.address' => 'nullable|string|max:500',
            'objective' => 'nullable|string|max:1000',
            'education' => 'nullable|array',
            'work_experience' => 'nullable|array',
            'skills' => 'nullable|array',
            'certifications' => 'nullable|array',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $personalInfo = $validated['personal_info'];
        $personalInfo['job_title'] = isset($personalInfo['job_title']) ? trim($personalInfo['job_title']) : null;
        $personalInfo['job_title'] = $personalInfo['job_title'] === '' ? null : $personalInfo['job_title'];
        $personalInfo['phone'] = isset($personalInfo['phone']) ? trim($personalInfo['phone']) : null;
        $personalInfo['address'] = isset($personalInfo['address']) ? trim($personalInfo['address']) : null;

        $education = collect($validated['education'] ?? [])->map(function ($edu) {
            return [
                'institution' => trim($edu['institution'] ?? ''),
                'degree' => trim($edu['degree'] ?? ''),
                'department' => trim($edu['department'] ?? ''),
                'year' => trim($edu['year'] ?? ''),
            ];
        })->filter(function ($edu) {
            return $edu['institution'] !== '' || $edu['degree'] !== '' || $edu['department'] !== '' || $edu['year'] !== '';
        })->values()->all();

        $workExperience = collect($validated['work_experience'] ?? [])->map(function ($exp) {
            return [
                'company' => trim($exp['company'] ?? ''),
                'position' => trim($exp['position'] ?? ''),
                'start_date' => trim($exp['start_date'] ?? ''),
                'end_date' => trim($exp['end_date'] ?? ''),
                'description' => trim($exp['description'] ?? ''),
            ];
        })->filter(function ($exp) {
            return $exp['company'] !== '' || $exp['position'] !== '' || $exp['start_date'] !== '' || $exp['end_date'] !== '' || $exp['description'] !== '';
        })->values()->all();

        $skills = collect($validated['skills'] ?? [])->map(fn ($skill) => trim($skill ?? ''))->filter()->values()->all();

        $certifications = collect($validated['certifications'] ?? [])->map(function ($cert) {
            $name = trim($cert['name'] ?? '');
            return $name ? ['name' => $name] : null;
        })->filter()->values()->all();

        if ($request->hasFile('profile_image')) {
            if ($resume->profile_image) {
                Storage::disk('public')->delete($resume->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('resume-images', 'public');
        } else {
            unset($validated['profile_image']);
        }

        $resume->update([
            'personal_info' => $personalInfo,
            'objective' => isset($validated['objective']) ? trim($validated['objective']) : null,
            'education' => $education,
            'work_experience' => $workExperience,
            'skills' => $skills,
            'certifications' => $certifications,
            'profile_image' => $validated['profile_image'] ?? $resume->profile_image,
        ]);

        return redirect()->route('resume.index')->with('success', 'Resume updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resume $resume)
    {
        $user = Auth::user();
        
        if ($resume->user_id !== $user->id) {
            abort(403);
        }

        // Delete profile image if exists
        if ($resume->profile_image) {
            Storage::disk('public')->delete($resume->profile_image);
        }

        $resume->delete();

        return redirect()->route('resume.index')->with('success', 'Resume deleted successfully!');
    }

    /**
     * Generate and download filled PDF
     */
    public function download(Resume $resume)
    {
        $user = Auth::user();
        
        if ($resume->user_id !== $user->id) {
            abort(403);
        }

        $templatePath = Storage::disk('local')->path('resume-templates/template.pdf');
        
        if (!file_exists($templatePath)) {
            abort(404, 'Template not found');
        }

        try {
            $pdf = new \setasign\Fpdi\Fpdi();
            $pageCount = $pdf->setSourceFile($templatePath);
            $tplId = $pdf->importPage(1);
            
            // Get page dimensions
            $size = $pdf->getTemplateSize($tplId);
            $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false);
            
            // Set font
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor(0, 0, 0); // Black text
            
            // Convert inches to millimeters (FPDF default unit)
            // Coordinates provided by user are in inches
            $inchToMm = function($inch) {
                return $inch * 25.4;
            };
            $pointsToMm = function($pt) {
                return $pt * 25.4 / 72;
            };
            
            // Calculate text box widths based on template layout
            // Standard page width is 8.5 inches = 612 points
            // Left column (personal info, contact, skills) is approximately 2.5 inches wide
            // Right column (name, summary, education, experience) is approximately 4.5 inches wide
            $leftColumnWidth = $inchToMm(2.35);  // Width for left column sections
            $rightColumnWidth = $inchToMm(3.85);  // Width for right column sections, leave right margin
            $bodyFontPt = 14;
            $lineHeight = $pointsToMm($bodyFontPt * 1.25);
            $bulletChar = chr(149); // CP1252 bullet character
            
            // Format data for display
            $name = trim($resume->personal_info['name'] ?? '');
            $jobTitle = trim($resume->personal_info['job_title'] ?? '');
            $email = trim($resume->personal_info['email'] ?? '');
            $phone = trim($resume->personal_info['phone'] ?? '');
            $address = trim($resume->personal_info['address'] ?? '');
            $objective = trim($resume->objective ?? '');
            
            // Format education - cleaner formatting
            $educationText = '';
            if (!empty($resume->education)) {
                foreach ($resume->education as $index => $edu) {
                    if ($index > 0) $educationText .= "\n";
                    $institution = trim($edu['institution'] ?? '');
                    $degree = trim($edu['degree'] ?? '');
                    $department = trim($edu['department'] ?? '');
                    $year = trim($edu['year'] ?? '');
                    
                    if ($institution) $educationText .= $institution . "\n";
                    if ($degree) {
                        $educationText .= $degree;
                        if ($department) $educationText .= ' - ' . $department;
                        $educationText .= "\n";
                    }
                    if ($year) $educationText .= $year . "\n";
                }
            }
            
            // Format work experience - cleaner formatting
            $experienceText = '';
            if (!empty($resume->work_experience)) {
                $experienceBlocks = [];
                foreach ($resume->work_experience as $exp) {
                    $position = trim($exp['position'] ?? '');
                    $company = trim($exp['company'] ?? '');
                    $startDate = trim($exp['start_date'] ?? '');
                    $endDate = trim($exp['end_date'] ?? '');
                    $description = trim($exp['description'] ?? '');

                    if (empty($position) && empty($company) && empty($startDate) && empty($endDate) && empty($description)) {
                        continue;
                    }

                    $lines = [];
                    $titleLine = $bulletChar . ' ';
                    if ($position) {
                        $titleLine .= $position;
                    }
                    if ($company) {
                        $titleLine .= ($position ? ', ' : '') . $company;
                    }
                    if ($titleLine === $bulletChar . ' ') {
                        $titleLine .= 'Experience';
                    }
                    $lines[] = $titleLine;

                    if ($startDate || $endDate) {
                        $lines[] = '  ' . ($startDate ?: 'N/A') . ' - ' . ($endDate ?: 'Present');
                    }

                    if ($description) {
                        $lines[] = '  ' . $description;
                    }

                    $experienceBlocks[] = implode("\n", $lines);
                }
                $experienceText = implode("\n\n", $experienceBlocks);
            }
            
            // Format skills - one per line or comma separated
            $skillsText = '';
            if (!empty($resume->skills)) {
                $filteredSkills = array_filter(array_map('trim', $resume->skills));
                if (!empty($filteredSkills)) {
                    $skillsText = implode("\n", array_map(fn($skill) => $bulletChar . ' ' . $skill, $filteredSkills));
                }
            }
            
            // Format certifications (only names now)
            $certificationsText = '';
            if (!empty($resume->certifications)) {
                $certNames = array_filter(array_map(function($cert) {
                    return trim($cert['name'] ?? '');
                }, $resume->certifications));
                if (!empty($certNames)) {
                    $certificationsText = implode("\n", array_map(fn($cert) => $bulletChar . ' ' . $cert, $certNames));
                }
            }
            
            // Draw text at specified coordinates (converting inches to points)
            // {{ name }} x 3.77 and y 1.70
            if ($name) {
                $pdf->SetXY($inchToMm(3.69), $inchToMm(1.12));
                $pdf->SetFont('Times', 'B', 40); // 40pt as per template label
                $pdf->Cell($rightColumnWidth, $pointsToMm(40), $name, 0, 1, 'L');
            }
            
            // {{ job title }} x 3.81 and y 1.74
            if ($jobTitle) {
                $pdf->SetXY($inchToMm(3.81), $inchToMm(1.74));
                $pdf->SetFont('Times', '', 24);
                $pdf->Cell($rightColumnWidth, $pointsToMm(24), $jobTitle, 0, 1, 'L');
            }
 
            $pdf->SetFont('Times', '', $bodyFontPt);
            if ($email) {
                $pdf->SetXY($inchToMm(0.62), $inchToMm(4.08));
                $pdf->MultiCell($leftColumnWidth, $lineHeight, 'Email: ' . $email, 0, 'L');
            }
            if ($phone) {
                $pdf->SetXY($inchToMm(0.62), $inchToMm(4.58));
                $pdf->MultiCell($leftColumnWidth, $lineHeight, 'Phone: ' . $phone, 0, 'L');
            }
            if ($address) {
                $pdf->SetXY($inchToMm(0.62), $inchToMm(5.08));
                $pdf->MultiCell($leftColumnWidth, $lineHeight, 'Address: ' . $address, 0, 'L');
            }
 
            // {{ Summary }} x 3.76 and y 3.01
            if ($objective) {
                $pdf->SetXY($inchToMm(3.76), $inchToMm(3.01));
                $pdf->SetFont('Times', '', $bodyFontPt);
                $pdf->MultiCell($rightColumnWidth, $lineHeight, $objective, 0, 'L');
            }
 
            // {{ education }} x 3.71 and y 5.14
            if ($educationText) {
                $pdf->SetXY($inchToMm(3.71), $inchToMm(5.14));
                $pdf->SetFont('Times', '', $bodyFontPt);
                $pdf->MultiCell($rightColumnWidth, $lineHeight, trim($educationText), 0, 'L');
            }
 
            // {{ experience }} x 3.71 and y 9.08
            if ($experienceText) {
                $pdf->SetXY($inchToMm(3.71), $inchToMm(9.08));
                $pdf->SetFont('Times', '', $bodyFontPt);
                $pdf->MultiCell($rightColumnWidth, $lineHeight, trim($experienceText), 0, 'L');
            }
 
            // {{ skills }} x 0.64 and y 6.65
            if ($skillsText) {
                $pdf->SetXY($inchToMm(0.64), $inchToMm(6.65));
                $pdf->SetFont('Times', '', $bodyFontPt);
                $pdf->MultiCell($leftColumnWidth, $lineHeight, $skillsText, 0, 'L');
            }
 
            // {{ certifications }} x 0.82 and y 10.13 (unchanged)
            if (!empty($certificationsText)) {
                $pdf->SetXY($inchToMm(0.82), $inchToMm(10.13));
                $pdf->SetFont('Times', '', $bodyFontPt);
                $pdf->MultiCell($leftColumnWidth, $lineHeight, $certificationsText, 0, 'L');
            }

            // {{ Image }} x 0.34 and y 0.68 - Add profile image
            if ($resume->profile_image) {
                $imagePath = Storage::disk('public')->path($resume->profile_image);
                if (file_exists($imagePath)) {
                    try {
                        $imageDiameterMm = $inchToMm(2.45);
                        $processedImage = $this->createCircularImage($imagePath, $imageDiameterMm);
                        $imageToUse = $processedImage ?: $imagePath;
                        $pdf->Image($imageToUse, $inchToMm(0.68), $inchToMm(0.69), $imageDiameterMm, $imageDiameterMm);
                        if ($processedImage && file_exists($processedImage)) {
                            @unlink($processedImage);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Could not add image to PDF: ' . $e->getMessage());
                    }
                }
            }
            
            $filename = 'resume_' . str_replace(' ', '_', $name ?: 'resume') . '.pdf';
            $pdfContent = $pdf->Output('S');

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Length', strlen($pdfContent))
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Create a circular cropped PNG version of the given image sized close to the target diameter.
     */
    protected function createCircularImage(string $imagePath, float $targetDiameterMm): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $imageData = @file_get_contents($imagePath);
        if ($imageData === false) {
            return null;
        }

        $src = @imagecreatefromstring($imageData);
        if (!$src) {
            return null;
        }

        $width = imagesx($src);
        $height = imagesy($src);
        $size = min($width, $height);
        if ($size <= 0) {
            imagedestroy($src);
            return null;
        }

        // Crop to square
        $square = imagecreatetruecolor($size, $size);
        imagealphablending($square, true);
        imagesavealpha($square, true);
        $transparent = imagecolorallocatealpha($square, 0, 0, 0, 127);
        imagefill($square, 0, 0, $transparent);

        $srcX = (int) max(0, ($width - $size) / 2);
        $srcY = (int) max(0, ($height - $size) / 2);
        imagecopyresampled($square, $src, 0, 0, $srcX, $srcY, $size, $size, $size, $size);
        imagedestroy($src);

        // Resize to target pixel size based on requested diameter (assume ~300 DPI)
        $targetPixels = max(200, (int) round(($targetDiameterMm / 25.4) * 300));
        $circle = imagecreatetruecolor($targetPixels, $targetPixels);
        imagealphablending($circle, false);
        imagesavealpha($circle, true);
        $transparentCircle = imagecolorallocatealpha($circle, 0, 0, 0, 127);
        imagefill($circle, 0, 0, $transparentCircle);

        imagecopyresampled($circle, $square, 0, 0, 0, 0, $targetPixels, $targetPixels, $size, $size);
        imagedestroy($square);

        // Apply circular mask
        $radius = $targetPixels / 2;
        for ($x = 0; $x < $targetPixels; $x++) {
            for ($y = 0; $y < $targetPixels; $y++) {
                $dx = $x - $radius;
                $dy = $y - $radius;
                if (($dx * $dx + $dy * $dy) > ($radius * $radius)) {
                    imagesetpixel($circle, $x, $y, $transparentCircle);
                }
            }
        }

        imagesavealpha($circle, true);

        $tmpDir = storage_path('app/resume-templates/tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }

        $tempPath = $tmpDir . '/' . uniqid('profile_', true) . '.png';
        if (!imagepng($circle, $tempPath)) {
            imagedestroy($circle);
            return null;
        }

        imagedestroy($circle);

        return $tempPath;
    }
}
