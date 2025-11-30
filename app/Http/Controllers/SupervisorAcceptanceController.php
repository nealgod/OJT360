<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceLetter;
use App\Models\DocumentRequirement;
use App\Models\StudentDocumentSubmission;
use App\Models\User;
use App\Services\PrePlacementService;
use App\Support\ProgramCodeResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupervisorAcceptanceController extends Controller
{
    /**
     * Show list of generated acceptance letters
     */
    public function index()
    {
        $supervisor = Auth::user();

        // Get generated letters
        $generatedLetters = AcceptanceLetter::where('supervisor_user_id', $supervisor->id)
            ->with('student', 'company')
            ->latest()
            ->paginate(10);

        // Count of students supervised
        $studentsCount = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($q) use ($supervisor) {
                $q->where('supervisor_id', $supervisor->id);
            })
            ->count();

        return view('supervisor.acceptance.index', compact('generatedLetters', 'studentsCount'));
    }

    /**
     * Show student search form
     */
    public function searchForm()
    {
        return view('supervisor.students.search');
    }

    /**
     * List all students supervised by the authenticated supervisor
     */
    public function students()
    {
        $supervisor = Auth::user();

        $students = User::where('role', 'intern')
            ->whereHas('studentProfile', function ($query) use ($supervisor) {
                $query->where('supervisor_id', $supervisor->id);
            })
            ->with([
                'studentProfile.company',
                'acceptanceLetters' => function ($query) use ($supervisor) {
                    $query->where('supervisor_user_id', $supervisor->id)
                        ->latest('generated_at');
                },
            ])
            ->orderBy('name')
            ->paginate(10);

        return view('supervisor.students.index', compact('students'));
    }

    /**
     * Autocomplete API for student search (live search)
     */
    public function autocomplete(Request $request)
    {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            // Use JOIN for better performance instead of nested whereHas
            // Filter: Only students WITHOUT a supervisor
            $students = User::select('users.*')
                ->join('student_profiles', 'users.id', '=', 'student_profiles.user_id')
                ->where('users.role', 'intern')
                ->whereNull('student_profiles.supervisor_id')
                ->where(function($q) use ($query) {
                    $q->where('users.name', 'LIKE', '%'.$query.'%')
                      ->orWhere('student_profiles.student_id', 'LIKE', '%'.$query.'%');
                })
                ->with('studentProfile:user_id,student_id,course,department,supervisor_id,profile_image')
                ->limit(50)
                ->get()
                ->map(function ($student) {
                    $profileImage = null;
                    if ($student->studentProfile && $student->studentProfile->profile_image) {
                        $profileImage = Storage::url($student->studentProfile->profile_image);
                    }

                    return [
                        'id' => $student->id,
                        'student_id' => $student->studentProfile->student_id ?? '',
                        'name' => $student->name,
                        'course' => $student->studentProfile->course ?? 'N/A',
                        'department' => $student->studentProfile->department ?? 'N/A',
                        'has_supervisor' => false,
                        'profile_image' => $profileImage,
                        'initials' => substr($student->name, 0, 1),
                    ];
                });

            return response()->json($students);
        } catch (\Exception $e) {
            \Log::error('Autocomplete Error: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * Search for student by ID or Name
     */
    public function search(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
        ]);

        $search = trim($request->student_id);

        // Use JOIN for better performance - Filter: Only students WITHOUT a supervisor
        $baseQuery = User::select('users.*')
            ->join('student_profiles', 'users.id', '=', 'student_profiles.user_id')
            ->where('users.role', 'intern')
            ->whereNull('student_profiles.supervisor_id')
            ->with(['studentProfile', 'documentSubmissions.requirement']);

        // 1. Try exact match by student_id
        $student = (clone $baseQuery)->where('student_profiles.student_id', $search)->first();

        // 2. If not found, try searching by name
        if (! $student) {
            $studentsByName = (clone $baseQuery)->where('users.name', 'LIKE', "%{$search}%")->get();

            if ($studentsByName->count() === 1) {
                $student = $studentsByName->first();
            } elseif ($studentsByName->count() > 1) {
                return back()->with('error', "Multiple available students found matching '{$search}'. Please select a specific student from the suggestions list.");
            }
        }

        if (! $student) {
            return back()->with('error', 'Student not found or already has a supervisor. Please check the ID/Name.');
        }

        // Redirect to student view page
        return redirect()->route('supervisor.students.view', $student->id);
    }

    /**
     * View student details and documents
     */
    public function viewStudent($studentId)
    {
        $student = User::where('role', 'intern')
            ->where('id', $studentId)
            ->with(['studentProfile', 'documentSubmissions.requirement'])
            ->firstOrFail();

        // Check if student already has a different supervisor
        if ($student->studentProfile && $student->studentProfile->supervisor_id) {
            // If it's a different supervisor trying to view, block them
            if ((int) Auth::id() !== (int) $student->studentProfile->supervisor_id) {
                return redirect()->route('supervisor.students.search')
                    ->with('error', 'This student already has a supervisor. You cannot accept this student.');
            }
        }

        // Check if this supervisor has already generated a letter for this student
        $hasAcceptanceLetter = \App\Models\AcceptanceLetter::where('student_user_id', $student->id)
            ->where('supervisor_user_id', Auth::id())
            ->exists();

        return view('supervisor.students.view', compact('student', 'hasAcceptanceLetter'));
    }

    /**
     * Show acceptance letter form for student
     */
    public function acceptStudent($studentId)
    {
        $student = User::where('role', 'intern')
            ->where('id', $studentId)
            ->with('studentProfile')
            ->firstOrFail();

        // Check if student already has a supervisor
        if ($student->studentProfile && $student->studentProfile->supervisor_id && (int) $student->studentProfile->supervisor_id !== (int) Auth::id()) {
            return redirect()->route('supervisor.students.search')
                ->with('error', 'This student already has a supervisor.');
        }

        $supervisor = Auth::user();
        $company = $supervisor->supervisorProfile->company ?? null;

        return view('supervisor.students.generate', compact('student', 'supervisor', 'company'));
    }

    /**
     * Generate acceptance letter for student
     */
    public function generateLetter(Request $request, $studentId)
    {
        $student = User::where('role', 'intern')
            ->where('id', $studentId)
            ->with('studentProfile')
            ->firstOrFail();

        // Check if student already has a supervisor
        if ($student->studentProfile && $student->studentProfile->supervisor_id && (int) $student->studentProfile->supervisor_id !== (int) Auth::id()) {
            return back()->with('error', 'This student already has a supervisor.');
        }

        $user = Auth::user();

        if (! $user->isSupervisor()) {
            abort(403);
        }

        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'immediate_supervisor' => 'required|string|max:255',
            'effective_date' => 'required|date',
            'total_hours' => 'required|integer|min:1',
            'work_schedule' => 'required|array',
            'shift_start' => 'required',
            'shift_end' => 'required',
            'break_minutes' => 'nullable|integer|min:0',
            'signature_type' => 'required|in:typed,uploaded',
            'signature_data' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        // Add shift times and break minutes to work schedule
        $validated['work_schedule']['shift_start'] = $validated['shift_start'];
        $validated['work_schedule']['shift_end'] = $validated['shift_end'];
        $validated['work_schedule']['break_minutes'] = $validated['break_minutes'] ?? 0;

        // Generate document ID
        $documentId = 'ACC-'.date('Y').'-'.str_pad(AcceptanceLetter::count() + 1, 6, '0', STR_PAD_LEFT);

        // Generate PDF
        $letterPath = $this->generateAcceptanceLetterPDF($student, $validated, $documentId);

        // Create acceptance letter record
        $letter = AcceptanceLetter::create([
            'acceptance_request_id' => null,
            'student_user_id' => $student->id,
            'supervisor_user_id' => $user->id,
            'company_id' => $user->supervisorProfile->company_id,
            'job_title' => $validated['job_title'],
            'department' => $validated['department'],
            'immediate_supervisor' => $validated['immediate_supervisor'],
            'start_date' => $validated['effective_date'],
            'end_date' => null,
            'total_hours' => $validated['total_hours'],
            'work_schedule' => $validated['work_schedule'],
            'signature_type' => $validated['signature_type'],
            'signature_data' => $validated['signature_data'] ?? null,
            'additional_notes' => $validated['additional_notes'] ?? null,
            'letter_path' => $letterPath,
            'document_id' => $documentId,
        ]);

        $requirements = DocumentRequirement::where('name', 'LIKE', 'Letter of Acceptance%')
            ->whereIn('type', ['pre_placement', 'post_placement'])
            ->get();

        $prePlacementSubmissionCreated = false;

        foreach ($requirements as $requirement) {
            $existingSubmission = StudentDocumentSubmission::where('student_user_id', $student->id)
                ->where('document_requirement_id', $requirement->id)
                ->whereIn('status', ['submitted', 'pending', 'approved'])
                ->exists();

            if ($existingSubmission) {
                continue;
            }

            StudentDocumentSubmission::create([
                'student_user_id' => $student->id,
                'document_requirement_id' => $requirement->id,
                'file_path' => $letterPath,
                'original_filename' => sprintf('acceptance_letter_%s_%s.pdf', $requirement->type, $documentId),
                'file_size' => Storage::disk('public')->size($letterPath),
                'mime_type' => 'application/pdf',
                'status' => 'submitted',
            ]);

            if ($requirement->type === 'pre_placement') {
                $prePlacementSubmissionCreated = true;
            }
        }

        if ($prePlacementSubmissionCreated) {
            PrePlacementService::recalculateForStudent($student->id);
        }

        // Link supervisor to student
        if ($student->studentProfile) {
            $student->studentProfile->update([
                'supervisor_id' => $user->id,
                'company_id' => $user->supervisorProfile->company_id,
            ]);
        }

        // Send notification to student
        try {
            $student->notify(new \App\Notifications\AcceptanceLetterGenerated($letter));
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification: '.$e->getMessage());
        }

        // Create in-app notification for student
        \App\Models\Notification::create([
            'user_id' => $student->id,
            'type' => 'acceptance_letter_generated',
            'title' => '✅ Acceptance Letter Generated',
            'message' => 'Your supervisor has generated your OJT Acceptance Letter for '.$letter->job_title.' at '.$letter->company->name.'. The letter is now available in your documents.',
            'data' => [
                'letter_id' => $letter->id,
                'document_id' => $letter->document_id,
                'company' => $letter->company->name,
                'position' => $letter->job_title,
                'supervisor' => $letter->immediate_supervisor,
                'start_date' => $letter->start_date->format('M d, Y'),
                'action_url' => route('acceptance-letters.download', $letter),
                'action_text' => 'View Letter',
            ],
            'read' => false,
        ]);

        // Notify coordinator
        if ($student->studentProfile && $student->studentProfile->department && $student->studentProfile->course) {
            $coordinator = \App\Models\User::whereHas('coordinatorProfile', function ($query) use ($student) {
                $query->where('department', $student->studentProfile->department)
                      ->whereHas('program', function ($q) use ($student) {
                          $q->where('name', $student->studentProfile->course);
                      });
            })->first();

            if ($coordinator) {
                try {
                    $coordinator->notify(new \App\Notifications\AcceptanceLetterGenerated($letter));
                } catch (\Exception $e) {
                    \Log::error('Failed to send email notification to coordinator: '.$e->getMessage());
                }

                \App\Models\Notification::create([
                    'user_id' => $coordinator->id,
                    'type' => 'acceptance_letter_generated',
                    'title' => '✅ Acceptance Letter Generated',
                    'message' => 'A supervisor has generated an acceptance letter for '.$student->name.' ('.$letter->job_title.' at '.$letter->company->name.').',
                    'data' => [
                        'letter_id' => $letter->id,
                        'document_id' => $letter->document_id,
                        'student_name' => $student->name,
                        'student_id' => $student->studentProfile->student_id,
                        'company' => $letter->company->name,
                        'position' => $letter->job_title,
                        'supervisor' => $letter->immediate_supervisor,
                        'start_date' => $letter->start_date->format('M d, Y'),
                        'action_url' => route('acceptance-letters.download', $letter),
                        'action_text' => 'View Letter',
                    ],
                    'read' => false,
                ]);
            }
        }

        return redirect()->route('supervisor.students.success', $letter->id)
            ->with('success', 'Acceptance letter generated successfully!');
    }

    /**
     * Generate PDF for acceptance letter
     */
    private function generateAcceptanceLetterPDF($student, $data, $documentId)
    {
        $studentProfile = $student->studentProfile;
        $supervisor = Auth::user();
        $company = $supervisor->supervisorProfile->company;

        // Create PDF using FPDI
        $pdf = new \setasign\Fpdi\Fpdi();

        // Get appropriate template based on student's program
        $templatePath = $this->getTemplatePath($studentProfile->course);

        if (file_exists($templatePath)) {
            $pdf->setSourceFile($templatePath);
            $tplId = $pdf->importPage(1);

            // Get page dimensions from template
            $size = $pdf->getTemplateSize($tplId);
            $pdf->addPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false);
            $pdf->SetTextColor(0, 0, 0);

            $program = $studentProfile?->course ?? '';
            $studentDepartment = $studentProfile?->department ?? '';
            $companyName = $company->name ?? '';
            $companyAddress = $company->address ?? '';
            $immediateSupervisor = trim($data['immediate_supervisor'] ?? '') ?: $supervisor->name;
            $supervisorPosition = $supervisor->supervisorProfile->position ?? '';
            $supervisorDepartment = $data['department'] ?? ($supervisor->supervisorProfile->department ?? '');
            $supervisorContact = $supervisor->supervisorProfile->phone ?: $supervisor->email;
            $schedule = $this->formatWorkSchedule($data['work_schedule']);
            $totalHours = $data['total_hours'].' hours';
            $effectiveDate = date('M d, Y', strtotime($data['effective_date']));

            $writeField = function (float $x, float $y, ?string $text, array $options = []) use ($pdf) {
                $content = trim((string) ($text ?? ''));
                if ($content === '') {
                    return;
                }
                $defaults = [
                    'width' => 80,
                    'lineHeight' => 5,
                    'align' => 'L',
                    'font' => ['Arial', '', 11],
                ];
                $options = array_merge($defaults, $options);
                [$family, $style, $size] = $options['font'];
                $pdf->SetFont($family, $style, $size);
                $pdf->SetXY($x, $y);
                $pdf->MultiCell($options['width'], $options['lineHeight'], $content, 0, $options['align']);
            };

            // Header section
            $writeField(1.23 * 25.4, 1.97 * 25.4, ': '.now()->format('F d, Y'), ['width' => 70]);
            $writeField(0.76 * 25.4, 2.70 * 25.4, $student->name, ['width' => 95, 'font' => ['Arial', 'B', 13]]);
            $writeField(3.21 * 25.4, 3.55 * 25.4, $companyName, [
                'width' => 80,
                'align' => 'C',
                'font' => ['Arial', 'B', 12],
            ]);
            $writeField(0.72 * 25.4, 4.00 * 25.4, $companyAddress, [
                'width' => 120,
                'font' => ['Arial', '', 12],
            ]);

            // Assignment table (right column)
            $tableX = 4.00 * 25.4;
            $tableWidth = 90;
            $writeField($tableX, 5.18 * 25.4, $data['job_title'] ?? '', ['width' => $tableWidth]);
            $writeField($tableX, 5.51 * 25.4, $data['department'] ?? '', ['width' => $tableWidth]);
            $writeField($tableX, 5.85 * 25.4, $immediateSupervisor, ['width' => $tableWidth]);
            $writeField($tableX, 6.14 * 25.4, $schedule, ['width' => $tableWidth]);
            $writeField($tableX, 6.45 * 25.4, $totalHours, ['width' => $tableWidth]);
            $writeField($tableX, 6.76 * 25.4, $effectiveDate, ['width' => $tableWidth]);

            // Signature section
            $writeField(0.62 * 25.4, 7.99 * 25.4, $immediateSupervisor, [
                'width' => 85,
                'font' => ['Arial', 'B', 12],
            ]);
            $writeField(0.72 * 25.4, 9.34 * 25.4, $supervisorPosition, [
                'width' => 85,
                'font' => ['Arial', 'B', 11],
            ]);
            $writeField(0.54 * 25.4, 10.30 * 25.4, $supervisorDepartment, [
                'width' => 85,
                'font' => ['Arial', 'B', 11],
            ]);
            $writeField(0.73 * 25.4, 11.05 * 25.4, $supervisorContact, [
                'width' => 85,
                'font' => ['Arial', 'B', 11],
            ]);

            $writeField(4.55 * 25.4, 7.93 * 25.4, $student->name, [
                'width' => 60,
                'align' => 'L',
                'font' => ['Arial', 'B', 12],
            ]);
        } else {
            // Fallback: Generate simple PDF without template
            $pdf->AddPage('P', [215.9, 330.2]);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'OJT ACCEPTANCE FORM', 0, 1, 'C');
            $pdf->Ln(10);

            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 8, 'Date: '.now()->format('F d, Y'), 0, 1);
            $pdf->Ln(5);

            $pdf->MultiCell(0, 6, "This is to signify the approval of on-the-job request allowing {$student->name}, a {$studentProfile->course} student, to render practicum at {$company->name}.");
            $pdf->Ln(10);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, 'Job Assignment Details:', 0, 1);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(60, 7, 'Job Title:', 0, 0);
            $pdf->Cell(0, 7, $data['job_title'], 0, 1);
            $pdf->Cell(60, 7, 'Department:', 0, 0);
            $pdf->Cell(0, 7, $data['department'] ?? 'N/A', 0, 1);
            $pdf->Cell(60, 7, 'Immediate Supervisor:', 0, 0);
            $pdf->Cell(0, 7, $data['immediate_supervisor'], 0, 1);
            $pdf->Cell(60, 7, 'Total Hours:', 0, 0);
            $pdf->Cell(0, 7, $data['total_hours'].' hours', 0, 1);
            $pdf->Cell(60, 7, 'Effective Date:', 0, 0);
            $pdf->Cell(0, 7, date('M d, Y', strtotime($data['effective_date'])), 0, 1);
            $pdf->Ln(10);

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, 'Company Representative:', 0, 1);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 7, $supervisor->name, 0, 1);
            $pdf->Cell(0, 7, $supervisor->supervisorProfile->position ?? '', 0, 1);
            $pdf->Cell(0, 7, $supervisor->email, 0, 1);
        }

        // Save PDF
        $filename = 'acceptance_letter_'.$documentId.'.pdf';
        $path = 'acceptance-letters/'.$filename;
        $fullPath = storage_path('app/public/'.$path);

        $directory = dirname($fullPath);
        if (! file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->Output('F', $fullPath);

        return $path;
    }

    /**
     * Format work schedule for display in PDF
     */
    private function formatWorkSchedule($schedule)
    {
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

        $days = [];
        foreach ($schedule as $day => $data) {
            if ($day === 'shift_start' || $day === 'shift_end') {
                continue;
            }
            if (isset($data['enabled']) && $data['enabled']) {
                $days[] = $dayAbbreviations[$day] ?? ucfirst($day);
            }
        }

        if (empty($days)) {
            return 'Not specified';
        }

        // Convert 24-hour format to 12-hour format
        $start = $schedule['shift_start'] ?? '08:00';
        $end = $schedule['shift_end'] ?? '17:00';

        $startTime = date('g:i A', strtotime($start));
        $endTime = date('g:i A', strtotime($end));

        return implode(', ', $days).' ('.$startTime.' - '.$endTime.')';
    }

    /**
     * Show success page after generating letter
     */
    public function showSuccess($letterId)
    {
        $letter = AcceptanceLetter::with('student', 'company')->findOrFail($letterId);

        // Ensure the logged-in supervisor owns this letter
        if ((int) $letter->supervisor_user_id !== (int) Auth::id()) {
            abort(403);
        }

        return view('supervisor.students.success', compact('letter'));
    }

    /**
     * Get the appropriate template path based on student's course
     */
    private function getTemplatePath($course)
    {
        $programCode = ProgramCodeResolver::resolve($course);
        $templatePath = resource_path("templates/{$programCode}-acceptance-letter.pdf");

        if (file_exists($templatePath)) {
            \Log::info("Using template for {$programCode}: {$templatePath}");

            return $templatePath;
        }

        // Fallback to BSIT template if program-specific not found
        $fallbackPath = resource_path('templates/BSIT-acceptance-letter.pdf');

        if (file_exists($fallbackPath)) {
            \Log::warning("Template not found for {$programCode}, using BSIT template");

            return $fallbackPath;
        }

        // Last resort: use old template name
        $legacyPath = resource_path('templates/BSITacceptancelettertemplate.pdf');
        \Log::warning("Using legacy BSIT template for {$programCode}");

        return $legacyPath;
    }
}
