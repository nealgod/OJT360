<?php

namespace App\Http\Controllers;

use App\Models\ApplicationLetter;
use App\Models\Resume;
use App\Support\ProgramCodeResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentDocumentController extends Controller
{
    /**
     * Display the student documents hub
     */
    public function index()
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            abort(403, 'Only students can access student documents.');
        }

        $resumes = Resume::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $applicationLetters = ApplicationLetter::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        // Check if there's an active submission for Resume/PDS
        $resumeRequirement = \App\Models\DocumentRequirement::where('name', 'LIKE', '%Resume%')
            ->orWhere('name', 'LIKE', '%PDS%')
            ->where('type', 'pre_placement')
            ->first();

        $hasActiveResumeSubmission = false;
        if ($resumeRequirement) {
            $hasActiveResumeSubmission = \App\Models\StudentDocumentSubmission::where('student_user_id', $user->id)
                ->where('document_requirement_id', $resumeRequirement->id)
                ->whereIn('status', ['submitted', 'pending'])
                ->exists();
        }

        // Check if there's an active submission for Application Letter
        $letterRequirement = \App\Models\DocumentRequirement::where('name', 'LIKE', '%Application Letter%')
            ->where('type', 'pre_placement')
            ->first();

        $hasActiveLetterSubmission = false;
        if ($letterRequirement) {
            $hasActiveLetterSubmission = \App\Models\StudentDocumentSubmission::where('student_user_id', $user->id)
                ->where('document_requirement_id', $letterRequirement->id)
                ->whereIn('status', ['submitted', 'pending'])
                ->exists();
        }

        return view('student-documents.index', compact('resumes', 'applicationLetters', 'hasActiveResumeSubmission', 'hasActiveLetterSubmission'));
    }

    // ==================== RESUME METHODS ====================

    /**
     * Show the form for creating a new resume
     */
    public function createResume()
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            abort(403, 'Only students can create resumes.');
        }

        // Auto-fill from student profile
        $studentProfile = $user->studentProfile;
        $yearLevels = ProgramCodeResolver::yearLevels();
        $yearLabel = $studentProfile && $studentProfile->year_level
            ? ($yearLevels[$studentProfile->year_level] ?? null)
            : null;

        $defaultData = [
            'personal_info' => [
                'name' => $user->name,
                'job_title' => '',
                'email' => $user->email,
                'phone' => $studentProfile->phone ?? '',
                'address' => $studentProfile->address ?? '',
            ],
            'education' => [
                [
                    'institution' => 'Eastern Visayas State University',
                    'degree' => $studentProfile->course ?? '',
                    'department' => $studentProfile->department ?? '',
                    'year_level' => $yearLabel,
                ],
            ],
        ];

        return view('student-documents.resume.create', [
            'defaultData' => $defaultData,
            'yearLevels' => $yearLevels,
        ]);
    }

    /**
     * Store a newly created resume
     */
    public function storeResume(Request $request)
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            abort(403);
        }

        $validated = $request->validate([
            'personal_info' => 'required|array',
            'personal_info.name' => 'required|string|max:255',
            'personal_info.job_title' => 'nullable|string|max:255',
            'personal_info.email' => 'required|email|max:255',
            'personal_info.phone' => 'nullable|string|max:50',
            'personal_info.address' => 'nullable|string|max:500',
            'objective' => 'nullable|string|max:250',
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
            $type = trim($edu['type'] ?? 'college');
            $data = [
                'type' => $type,
                'institution' => trim($edu['institution'] ?? ''),
            ];

            if ($type === 'college') {
                $data['degree'] = trim($edu['degree'] ?? '');
                $data['department'] = trim($edu['department'] ?? '');
                $data['year_level'] = trim($edu['year_level'] ?? '');
            } elseif ($type === 'senior_high') {
                $data['strand'] = trim($edu['strand'] ?? '');
                $data['year_period'] = trim($edu['year_period'] ?? '');
            } else {
                $data['year_period'] = trim($edu['year_period'] ?? '');
            }

            return $data;
        })->filter(function ($edu) {
            return ! empty($edu['institution']);
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

        return redirect()->route('student-documents.index')->with('success', 'Resume created successfully!');
    }

    /**
     * Show the form for editing a resume
     */
    public function editResume(Resume $resume)
    {
        $user = Auth::user();

        if ($resume->user_id !== $user->id) {
            abort(403);
        }

        return view('student-documents.resume.edit', [
            'resume' => $resume,
            'yearLevels' => ProgramCodeResolver::yearLevels(),
        ]);
    }

    /**
     * Update a resume
     */
    public function updateResume(Request $request, Resume $resume)
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
            'objective' => 'nullable|string|max:250',
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
            $type = trim($edu['type'] ?? 'college');
            $data = [
                'type' => $type,
                'institution' => trim($edu['institution'] ?? ''),
            ];

            if ($type === 'college') {
                $data['degree'] = trim($edu['degree'] ?? '');
                $data['department'] = trim($edu['department'] ?? '');
                $data['year_level'] = trim($edu['year_level'] ?? '');
            } elseif ($type === 'senior_high') {
                $data['strand'] = trim($edu['strand'] ?? '');
                $data['year_period'] = trim($edu['year_period'] ?? '');
            } else {
                $data['year_period'] = trim($edu['year_period'] ?? '');
            }

            return $data;
        })->filter(function ($edu) {
            return ! empty($edu['institution']);
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

        return redirect()->route('student-documents.index')->with('success', 'Resume updated successfully!');
    }

    /**
     * Delete a resume
     */
    public function destroyResume(Resume $resume)
    {
        $user = Auth::user();

        if ($resume->user_id !== $user->id) {
            abort(403);
        }

        if ($resume->profile_image) {
            Storage::disk('public')->delete($resume->profile_image);
        }

        $resume->delete();

        return redirect()->route('student-documents.index')->with('success', 'Resume deleted successfully!');
    }

    /**
     * Download resume PDF
     */
    public function downloadResume(Resume $resume)
    {
        $user = Auth::user();

        $canDownload = false;

        if ($resume->user_id === $user->id) {
            $canDownload = true;
        } elseif ($user->isSupervisor()) {
            $student = \App\Models\User::find($resume->user_id);
            if ($student && $student->studentProfile && $student->studentProfile->supervisor_id === $user->id) {
                $canDownload = true;
            }
        } elseif ($user->isCoordinator()) {
            // Coordinator can download only if the student exists and belongs to the same department
            $student = \App\Models\User::find($resume->user_id);
            $department = $user->coordinatorProfile?->department;
            if ($student && $student->studentProfile && $student->studentProfile->department === $department) {
                $canDownload = true;
            }
        }

        if (! $canDownload) {
            abort(403);
        }

        // Use the existing resume download logic from ResumeController
        return app(ResumeController::class)->download($resume);
    }

    // ==================== APPLICATION LETTER METHODS ====================

    /**
     * Show the form for creating an application letter
     */
    public function createApplicationLetter()
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            abort(403, 'Only students can create application letters.');
        }

        $studentProfile = $user->studentProfile;

        return view('student-documents.application-letter.create', [
            'user' => $user,
            'studentProfile' => $studentProfile,
        ]);
    }

    /**
     * Store a newly created application letter
     */
    public function storeApplicationLetter(Request $request)
    {
        $user = Auth::user();

        if (! $user->isStudent()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:50',
        ]);

        $content = trim($validated['content']);
        $content = preg_replace('/[ \t]+/', ' ', $content);
        $content = preg_replace("/(\r\n|\r|\n){3,}/", "\n\n", $content);

        ApplicationLetter::create([
            'user_id' => $user->id,
            'content' => $content,
        ]);

        return redirect()->route('student-documents.index')->with('success', 'Application letter created successfully!');
    }

    /**
     * Show the form for editing an application letter
     */
    public function editApplicationLetter(ApplicationLetter $letter)
    {
        $user = Auth::user();

        if ($letter->user_id !== $user->id) {
            abort(403);
        }

        $studentProfile = $user->studentProfile;

        return view('student-documents.application-letter.edit', [
            'letter' => $letter,
            'user' => $user,
            'studentProfile' => $studentProfile,
        ]);
    }

    /**
     * Update an application letter
     */
    public function updateApplicationLetter(Request $request, ApplicationLetter $letter)
    {
        $user = Auth::user();

        if ($letter->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:50',
        ]);

        $content = trim($validated['content']);
        $content = preg_replace('/[ \t]+/', ' ', $content);
        $content = preg_replace("/(\r\n|\r|\n){3,}/", "\n\n", $content);

        $letter->update([
            'content' => $content,
        ]);

        return redirect()->route('student-documents.index')->with('success', 'Application letter updated successfully!');
    }

    /**
     * Delete an application letter
     */
    public function destroyApplicationLetter(ApplicationLetter $letter)
    {
        $user = Auth::user();

        if ($letter->user_id !== $user->id) {
            abort(403);
        }

        $letter->delete();

        return redirect()->route('student-documents.index')->with('success', 'Application letter deleted successfully!');
    }

    /**
     * Submit resume to coordinator for review
     */
    public function submitResume(Resume $resume)
    {
        $user = Auth::user();

        if ($resume->user_id !== $user->id) {
            return back()->with('error', 'You can only submit your own documents.');
        }

        $requirements = \App\Models\DocumentRequirement::where(function ($query) {
            $query->where('name', 'LIKE', '%Resume%')
                ->orWhere('name', 'LIKE', '%PDS%');
        })
            ->whereIn('type', ['pre_placement', 'post_placement'])
            ->get()
            ->keyBy('type');

        $primaryRequirement = $requirements->get('pre_placement');

        if (! $primaryRequirement) {
            return back()->with('error', 'Resume document requirement not found. Please contact your coordinator.');
        }

        return \DB::transaction(function () use ($user, $resume, $requirements, $primaryRequirement) {
            $activeSubmissions = [];

            foreach ($requirements as $type => $requirement) {
                $activeSubmissions[$type] = \App\Models\StudentDocumentSubmission::where('student_user_id', $user->id)
                    ->where('document_requirement_id', $requirement->id)
                    ->whereIn('status', ['submitted', 'pending'])
                    ->lockForUpdate()
                    ->first();

                if ($type === 'pre_placement' && $activeSubmissions[$type]) {
                    return back()->with('error', 'You already have a resume submitted for review. Only ONE resume can be submitted at a time. Please wait for coordinator approval or cancellation before submitting another one.');
                }
            }

            if ($resume->submitted_to_documents) {
                return back()->with('error', 'This resume has already been submitted.');
            }

            try {
                $pdfContent = app(ResumeController::class)->download($resume)->getContent();
                $preSubmissionCreated = false;

                foreach ($requirements as $type => $requirement) {
                    if ($type !== 'pre_placement' && $activeSubmissions[$type]) {
                        continue;
                    }

                    $filename = sprintf(
                        'resume_%s_%d_%s.pdf',
                        $type,
                        $user->id,
                        now()->format('YmdHis').'_'.Str::random(4)
                    );

                    $path = 'document-submissions/'.$filename;
                    Storage::disk('public')->put($path, $pdfContent);

                    \App\Models\StudentDocumentSubmission::create([
                        'student_user_id' => $user->id,
                        'document_requirement_id' => $requirement->id,
                        'file_path' => $path,
                        'original_filename' => $filename,
                        'file_size' => strlen($pdfContent),
                        'mime_type' => 'application/pdf',
                    ]);

                    if ($type === 'pre_placement') {
                        $preSubmissionCreated = true;
                    }
                }

                if ($preSubmissionCreated) {
                    $resume->update([
                        'submitted_to_documents' => true,
                        'submitted_at' => now(),
                    ]);

                    $coordinator = \App\Models\User::where('role', 'coordinator')
                        ->whereHas('coordinatorProfile', function ($q) use ($user) {
                            $q->where('department', $user->studentProfile?->department);
                        })
                        ->first();

                    if ($coordinator) {
                        \App\Models\Notification::create([
                            'user_id' => $coordinator->id,
                            'type' => 'document_submitted',
                            'title' => 'New Resume Submission',
                            'message' => $user->name.' has submitted a resume for review.',
                            'data' => json_encode([
                                'student_id' => $user->id,
                                'requirement_id' => $primaryRequirement->id,
                            ]),
                        ]);
                    }
                }

                return back()->with('success', 'Resume submitted successfully! Your coordinator will review it.');
            } catch (\Exception $e) {
                \Log::error('Resume submission error: '.$e->getMessage());

                return back()->with('error', 'Failed to submit resume. Please try again or contact support.');
            }
        });
    }

    /**
     * Submit application letter to coordinator for review
     */
    public function submitApplicationLetter(ApplicationLetter $letter)
    {
        $user = Auth::user();

        if ($letter->user_id !== $user->id) {
            return back()->with('error', 'You can only submit your own documents.');
        }

        $requirements = \App\Models\DocumentRequirement::where('name', 'LIKE', '%Application Letter%')
            ->whereIn('type', ['pre_placement', 'post_placement'])
            ->get()
            ->keyBy('type');

        $primaryRequirement = $requirements->get('pre_placement');

        if (! $primaryRequirement) {
            return back()->with('error', 'Application Letter document requirement not found. Please contact your coordinator.');
        }

        return \DB::transaction(function () use ($user, $letter, $requirements, $primaryRequirement) {
            $activeSubmissions = [];

            foreach ($requirements as $type => $requirement) {
                $activeSubmissions[$type] = \App\Models\StudentDocumentSubmission::where('student_user_id', $user->id)
                    ->where('document_requirement_id', $requirement->id)
                    ->whereIn('status', ['submitted', 'pending'])
                    ->lockForUpdate()
                    ->first();

                if ($type === 'pre_placement' && $activeSubmissions[$type]) {
                    return back()->with('error', 'You already have an application letter submitted for review. Only ONE application letter can be submitted at a time. Please wait for coordinator approval or cancellation before submitting another one.');
                }
            }

            if ($letter->submitted_to_documents) {
                return back()->with('error', 'This application letter has already been submitted.');
            }

            try {
                $pdfContent = $this->downloadApplicationLetter($letter)->getContent();
                $preSubmissionCreated = false;

                foreach ($requirements as $type => $requirement) {
                    if ($type !== 'pre_placement' && $activeSubmissions[$type]) {
                        continue;
                    }

                    $filename = sprintf(
                        'application_letter_%s_%d_%s.pdf',
                        $type,
                        $user->id,
                        now()->format('YmdHis').'_'.Str::random(4)
                    );

                    $path = 'document-submissions/'.$filename;
                    Storage::disk('public')->put($path, $pdfContent);

                    \App\Models\StudentDocumentSubmission::create([
                        'student_user_id' => $user->id,
                        'document_requirement_id' => $requirement->id,
                        'file_path' => $path,
                        'original_filename' => $filename,
                        'file_size' => strlen($pdfContent),
                        'mime_type' => 'application/pdf',
                    ]);

                    if ($type === 'pre_placement') {
                        $preSubmissionCreated = true;
                    }
                }

                if ($preSubmissionCreated) {
                    $letter->update([
                        'submitted_to_documents' => true,
                        'submitted_at' => now(),
                    ]);

                    $coordinator = \App\Models\User::where('role', 'coordinator')
                        ->whereHas('coordinatorProfile', function ($q) use ($user) {
                            $q->where('department', $user->studentProfile?->department);
                        })
                        ->first();

                    if ($coordinator) {
                        \App\Models\Notification::create([
                            'user_id' => $coordinator->id,
                            'type' => 'document_submitted',
                            'title' => 'New Application Letter Submission',
                            'message' => $user->name.' has submitted an application letter for review.',
                            'data' => json_encode([
                                'student_id' => $user->id,
                                'requirement_id' => $primaryRequirement->id,
                            ]),
                        ]);
                    }
                }

                return back()->with('success', 'Application letter submitted successfully! Your coordinator will review it.');
            } catch (\Exception $e) {
                \Log::error('Application letter submission error: '.$e->getMessage());

                return back()->with('error', 'Failed to submit application letter. Please try again or contact support.');
            }
        });
    }

    /**
     * Download application letter as PDF
     */
    public function downloadApplicationLetter(ApplicationLetter $letter)
    {
        $user = Auth::user();

        $canDownload = false;

        if ($letter->user_id === $user->id) {
            $canDownload = true;
        } elseif ($user->isSupervisor()) {
            $student = \App\Models\User::find($letter->user_id);
            if ($student && $student->studentProfile && $student->studentProfile->supervisor_id === $user->id) {
                $canDownload = true;
            }
        } elseif ($user->isCoordinator()) {
    // Coordinator can download only if the student exists and belongs to the same department
    $student = \App\Models\User::find($letter->user_id);
    $department = $user->coordinatorProfile?->department;
    if ($student && $student->studentProfile && $student->studentProfile->department === $department) {
        $canDownload = true;
    }
        }

        if (! $canDownload) {
            abort(403);
        }

        $student = $letter->user;
        $studentProfile = $student->studentProfile;

        try {
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator('OJT360');
            $pdf->SetAuthor($student->name);
            $pdf->SetTitle('Application Letter');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins (1 inch = 25.4mm)
            $pdf->SetMargins(25.4, 25.4, 25.4);
            $pdf->SetAutoPageBreak(true, 25.4);

            // Add a page
            $pdf->AddPage();

            // Set font
            $pdf->SetFont('times', '', 12);

            // Date (right-aligned)
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(0, 10, date('F d, Y'), 0, 1, 'R');
            $pdf->Ln(5);

            // Student info (left-aligned)
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(0, 6, $student->name, 0, 1, 'L');
            if ($student->email) {
                $pdf->Cell(0, 6, $student->email, 0, 1, 'L');
            }
            if ($studentProfile && $studentProfile->phone) {
                $pdf->Cell(0, 6, $studentProfile->phone, 0, 1, 'L');
            }
            if ($studentProfile && $studentProfile->department) {
                $pdf->Cell(0, 6, $studentProfile->department, 0, 1, 'L');
            }
            if ($studentProfile && $studentProfile->course) {
                $pdf->Cell(0, 6, $studentProfile->course, 0, 1, 'L');
            }
            $pdf->Ln(10);

            // Title (centered, bold)
            $pdf->SetFont('times', 'B', 14);
            $pdf->Cell(0, 10, 'APPLICATION LETTER', 0, 1, 'C');
            $pdf->Ln(5);

            // Letter content
            $pdf->SetFont('times', '', 12);
            $paragraphs = preg_split("/(\r\n|\r|\n){2}/", $letter->content);
            foreach ($paragraphs as $index => $paragraph) {
                $pdf->MultiCell(0, 5, trim($paragraph), 0, 'L');
                if ($index < count($paragraphs) - 1) {
                    $pdf->Ln(3);
                }
            }
            $pdf->Ln(8);

            // Closing
            $pdf->SetFont('times', '', 12);
            $pdf->Cell(0, 6, 'Sincerely yours,', 0, 1, 'L');
            $pdf->Ln(6);

            // Signature line
            $pdf->SetFont('times', 'B', 12);
            $pdf->Cell(0, 6, $student->name, 0, 1, 'L');

            // Output PDF
            $filename = 'application_letter_'.str_replace(' ', '_', $student->name).'.pdf';
            $pdfContent = $pdf->Output('', 'S');

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Length', strlen($pdfContent))
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: '.$e->getMessage());
            abort(500, 'Error generating PDF: '.$e->getMessage());
        }
    }
}
