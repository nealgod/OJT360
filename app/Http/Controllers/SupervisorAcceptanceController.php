<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceLetter;
use App\Models\User;
use App\Models\StudentDocumentSubmission;
use App\Models\DocumentRequirement;
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
            ->whereHas('studentProfile', function($q) use ($supervisor) {
                $q->where('supervisor_id', $supervisor->id);
            })
            ->count();
        
        return view('supervisor.acceptance.index', compact('generatedLetters', 'studentsCount'));
    }

    /**
     * Show list of supervised students
     */
    public function students()
    {
        $supervisor = Auth::user();
        
        // Get all students supervised by this supervisor
        $students = User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($supervisor) {
                $q->where('supervisor_id', $supervisor->id);
            })
            ->with('studentProfile.company')
            ->paginate(15);
        
        return view('supervisor.students', compact('students'));
    }

    /**
     * Show student search form
     */
    public function searchForm()
    {
        return view('supervisor.students.search');
    }

    /**
     * Autocomplete API for student search (live search)
     */
    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Search students by student_id, limit to 5 results
        $students = User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($query) {
                $q->where('student_id', 'LIKE', '%' . $query . '%');
            })
            ->with(['studentProfile' => function($q) {
                $q->select('user_id', 'student_id', 'course', 'department', 'supervisor_id', 'profile_image');
            }])
            ->limit(5)
            ->get()
            ->map(function($student) {
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
                    'has_supervisor' => !is_null($student->studentProfile->supervisor_id ?? null),
                    'profile_image' => $profileImage,
                    'initials' => substr($student->name, 0, 1),
                ];
            });

        return response()->json($students);
    }

    /**
     * Search for student by ID
     */
    public function search(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
        ]);

        $studentId = trim($request->student_id);

        // Search for student by student_id in student_profiles table
        $student = User::where('role', 'intern')
            ->whereHas('studentProfile', function($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->with(['studentProfile', 'documentSubmissions.requirement'])
            ->first();

        if (!$student) {
            return back()->with('error', 'Student not found. Please check the Student ID and try again.');
        }

        // Check if student already has a supervisor
        if ($student->studentProfile && $student->studentProfile->supervisor_id) {
            $existingSupervisor = User::find($student->studentProfile->supervisor_id);
            return back()->with('error', 'This student already has a supervisor: ' . $existingSupervisor->name);
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

        // Check if student already has a supervisor
        if ($student->studentProfile && $student->studentProfile->supervisor_id) {
            $existingSupervisor = User::find($student->studentProfile->supervisor_id);
            
            // If current supervisor is viewing, allow it
            if (Auth::id() !== $student->studentProfile->supervisor_id) {
                return redirect()->route('supervisor.students.search')
                    ->with('error', 'This student already has a supervisor: ' . $existingSupervisor->name);
            }
        }

        return view('supervisor.students.view', compact('student'));
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
        if ($student->studentProfile && $student->studentProfile->supervisor_id && $student->studentProfile->supervisor_id !== Auth::id()) {
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
        if ($student->studentProfile && $student->studentProfile->supervisor_id && $student->studentProfile->supervisor_id !== Auth::id()) {
            return back()->with('error', 'This student already has a supervisor.');
        }

        $user = Auth::user();

        if (!$user->isSupervisor()) {
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
            'signature_type' => 'required|in:typed,uploaded',
            'signature_data' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);
        
        // Add shift times to work schedule
        $validated['work_schedule']['shift_start'] = $validated['shift_start'];
        $validated['work_schedule']['shift_end'] = $validated['shift_end'];

        // Generate document ID
        $documentId = 'ACC-' . date('Y') . '-' . str_pad(AcceptanceLetter::count() + 1, 6, '0', STR_PAD_LEFT);

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

        // Create document submission for student
        $requirement = DocumentRequirement::where('name', 'Letter of Acceptance')->first();
        
        if ($requirement) {
            StudentDocumentSubmission::create([
                'student_user_id' => $student->id,
                'document_requirement_id' => $requirement->id,
                'file_path' => $letterPath,
                'original_filename' => 'acceptance_letter_' . $documentId . '.pdf',
                'file_size' => Storage::disk('public')->size($letterPath),
                'mime_type' => 'application/pdf',
                'status' => 'submitted',
            ]);
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
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }
        
        // Create in-app notification for student
        \App\Models\Notification::create([
            'user_id' => $student->id,
            'type' => 'acceptance_letter_generated',
            'title' => '✅ Acceptance Letter Generated',
            'message' => 'Your supervisor has generated your OJT Acceptance Letter for ' . $letter->job_title . ' at ' . $letter->company->name . '. The letter is now available in your documents.',
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
            $coordinator = \App\Models\User::whereHas('coordinatorProfile', function($query) use ($student) {
                $query->where('department', $student->studentProfile->department)
                      ->whereHas('program', function($q) use ($student) {
                          $q->where('name', $student->studentProfile->course);
                      });
            })->first();
            
            if ($coordinator) {
                try {
                    $coordinator->notify(new \App\Notifications\AcceptanceLetterGenerated($letter));
                } catch (\Exception $e) {
                    \Log::error('Failed to send email notification to coordinator: ' . $e->getMessage());
                }
                
                \App\Models\Notification::create([
                    'user_id' => $coordinator->id,
                    'type' => 'acceptance_letter_generated',
                    'title' => '✅ Acceptance Letter Generated',
                    'message' => 'A supervisor has generated an acceptance letter for ' . $student->name . ' (' . $letter->job_title . ' at ' . $letter->company->name . ').',
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
        
        $templatePath = resource_path('templates/OJT ACCEPTANCE FORMtemplate.pdf');
        
        if (file_exists($templatePath)) {
            $pdf->AddPage('P', [215.9, 330.2]);
            $pdf->setSourceFile($templatePath);
            $tplId = $pdf->importPage(1);
            $pdf->useTemplate($tplId);
            
            $pdf->SetFont('Arial', '', 13);
            $pdf->SetTextColor(0, 0, 0);
            
            // Date
            $pdf->SetXY(25.4, 46.48);
            $pdf->Write(0, now()->format('F d, Y'));
            
            // Name
            $pdf->SetXY(30.99, 67.56);
            $pdf->Write(0, $student->name);
            
            // Program
            $pdf->SetXY(135.89, 76.45);
            $pdf->Write(0, $studentProfile->course ?? 'Information Technology');
            
            // Company
            $pdf->SetXY(91.19, 88.39);
            $pdf->Write(0, $company->name ?? '');
            
            // Location
            $pdf->SetXY(26.92, 99.31);
            $pdf->Write(0, $company->address ?? '');
            
            // Job Title
            $pdf->SetXY(109.98, 129.29);
            $pdf->Write(0, $data['job_title']);
            
            // Department
            $pdf->SetXY(109.98, 137.41);
            $pdf->Write(0, $data['department'] ?? '');
            
            // Working hours
            $schedule = $this->formatWorkSchedule($data['work_schedule']);
            $pdf->SetXY(109.98, 145.54);
            $pdf->Write(0, $schedule);
            
            // Total hours
            $pdf->SetXY(109.98, 161.80);
            $pdf->Write(0, $data['total_hours'] . ' hours');
            
            // Effective Date
            $pdf->SetXY(109.98, 169.93);
            $effectiveDate = date('M d, Y', strtotime($data['effective_date']));
            $pdf->Write(0, $effectiveDate);
            
            // Company Representative details (Left side - "Noted by:")
            $pdf->SetXY(30, 222);
            $pdf->Write(0, $supervisor->name);
            
            $pdf->SetXY(30, 252);
            $pdf->Write(0, $supervisor->supervisorProfile->position ?? '');
            
            $pdf->SetXY(30, 277);
            $pdf->Write(0, $data['department'] ?? '');
            
            $pdf->SetXY(30, 295);
            $pdf->Write(0, $supervisor->email);
            
            // Student Conforme section (Right side - "CONFORME:")
            $pdf->SetXY(135, 222);
            $pdf->Write(0, $student->name);
            
        } else {
            // Fallback: Generate simple PDF without template
            $pdf->AddPage('P', [215.9, 330.2]);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'OJT ACCEPTANCE FORM', 0, 1, 'C');
            $pdf->Ln(10);
            
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 8, 'Date: ' . now()->format('F d, Y'), 0, 1);
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
            $pdf->Cell(0, 7, $data['total_hours'] . ' hours', 0, 1);
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
        $filename = 'acceptance_letter_' . $documentId . '.pdf';
        $path = 'acceptance-letters/' . $filename;
        $fullPath = storage_path('app/public/' . $path);
        
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
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
            'sunday' => 'Sun'
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
        
        return implode(', ', $days) . ' (' . $startTime . ' - ' . $endTime . ')';
    }

    /**
     * Show success page after generating letter
     */
    public function showSuccess($letterId)
    {
        $letter = AcceptanceLetter::with('student', 'company')->findOrFail($letterId);
        
        // Ensure the logged-in supervisor owns this letter
        if ($letter->supervisor_user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('supervisor.students.success', compact('letter'));
    }
}
