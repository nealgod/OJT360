<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceRequest;
use App\Models\AcceptanceLetter;
use App\Models\User;
use App\Models\Company;
use App\Models\SupervisorProfile;
use App\Models\StudentDocumentSubmission;
use App\Models\DocumentRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SupervisorAcceptanceController extends Controller
{
    public function show($token)
    {
        // Find the acceptance request
        $request = AcceptanceRequest::where('token', $token)->first();
        
        if (!$request) {
            return view('supervisor.acceptance.error', [
                'error' => 'Invalid or expired link',
                'message' => 'This acceptance letter request link is invalid or has been removed.'
            ]);
        }

        // Check if already completed
        if ($request->status === 'completed') {
            return view('supervisor.acceptance.error', [
                'error' => 'Request Already Completed',
                'message' => 'This acceptance letter has already been generated. Please check your dashboard for the letter details.'
            ]);
        }

        // Check if expired
        if ($request->expires_at->isPast() || $request->status === 'expired') {
            return view('supervisor.acceptance.expired', compact('request', 'token'));
        }

        // Check if supervisor already has an account
        $supervisor = User::where('email', $request->supervisor_email)
            ->where('role', 'supervisor')
            ->first();

        if ($supervisor) {
            // Already has account, check if logged in
            if (Auth::check() && Auth::user()->id === $supervisor->id) {
                // Already logged in as this supervisor, go to form
                return redirect()->route('supervisor.acceptance.create', $token);
            }
            
            // Not logged in, redirect to main login page with intended URL
            session(['url.intended' => route('supervisor.acceptance.create', $token)]);
            return redirect()->route('login')->with('info', 'Please login to generate the acceptance letter.');
        }

        // Need to register
        return view('supervisor.acceptance.register', compact('request', 'token'));
    }

    public function register(Request $request, $token)
    {
        $acceptanceRequest = AcceptanceRequest::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'company_name' => 'required|string|max:255',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_phone' => 'nullable|string|max:20',
        ]);

        // Verify email matches the request
        if ($validated['email'] !== $acceptanceRequest->supervisor_email) {
            return back()->withErrors(['email' => 'Email cannot be changed.']);
        }

        // Update acceptance request with supervisor's name
        $acceptanceRequest->update([
            'supervisor_name' => $validated['name'],
        ]);

        // Create user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'supervisor',
            'email_verified_at' => now(), // Auto-verify since they clicked email link
        ]);

        // Create or find company
        $company = Company::firstOrCreate(
            ['name' => $validated['company_name']],
            [
                'address' => $validated['company_address'] ?? '',
                'contact_person' => $validated['name'], // Use supervisor's entered name
                'contact_email' => $acceptanceRequest->supervisor_email,
                'contact_phone' => $validated['company_phone'] ?? '',
            ]
        );

        // Create supervisor profile
        SupervisorProfile::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'position' => $validated['position'],
            'phone' => $validated['phone'],
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        // Log them in
        Auth::login($user);

        // Create notification for the new supervisor about their pending request
        try {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'acceptance_letter_request',
                'title' => '📝 Pending Acceptance Letter Request',
                'message' => $acceptanceRequest->student->name . ' is waiting for an acceptance letter for ' . $acceptanceRequest->position . ' at ' . $acceptanceRequest->company_name . '.',
                'data' => [
                    'request_id' => $acceptanceRequest->id,
                    'student_id' => $acceptanceRequest->student_user_id,
                    'student_name' => $acceptanceRequest->student->name,
                    'company' => $acceptanceRequest->company_name,
                    'position' => $acceptanceRequest->position,
                    'token' => $acceptanceRequest->token,
                    'action_url' => route('supervisor.acceptance.create', $acceptanceRequest->token),
                    'action_text' => 'Generate Letter',
                ],
                'read' => false,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create notification for supervisor: ' . $e->getMessage());
        }

        // Redirect to dashboard with success message
        return redirect()->route('dashboard')->with('success', 'Account created successfully! You have a pending acceptance letter request. Click "Acceptance Letters" to generate it.');
    }



    public function create($token)
    {
        $acceptanceRequest = AcceptanceRequest::where('token', $token)
            ->with('student.studentProfile')
            ->first();
        
        // Check if request exists
        if (!$acceptanceRequest) {
            return view('supervisor.acceptance.error')->with('error', 'Invalid or expired link.');
        }
        
        // Check if already completed
        if ($acceptanceRequest->status !== 'pending') {
            return view('supervisor.acceptance.error')->with('error', 'This request has already been processed.');
        }
        
        // Skip expiration check if supervisor is logged in - they can generate anytime
        // Only check expiration for non-authenticated access (initial registration flow)

        // Must be logged in as supervisor
        if (!Auth::check() || !Auth::user()->isSupervisor()) {
            return redirect()->route('supervisor.acceptance.show', $token);
        }

        return view('supervisor.acceptance.create', compact('acceptanceRequest', 'token'));
    }

    public function store(Request $request, $token)
    {
        $acceptanceRequest = AcceptanceRequest::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

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

        // TODO: Generate PDF using FPDI
        $letterPath = $this->generateAcceptanceLetterPDF($acceptanceRequest, $validated, $documentId);

        // Create acceptance letter record
        $letter = AcceptanceLetter::create([
            'acceptance_request_id' => $acceptanceRequest->id,
            'student_user_id' => $acceptanceRequest->student_user_id,
            'supervisor_user_id' => $user->id,
            'company_id' => $user->supervisorProfile->company_id,
            'job_title' => $validated['job_title'],
            'department' => $validated['department'],
            'immediate_supervisor' => $validated['immediate_supervisor'],
            'start_date' => $validated['effective_date'],
            'end_date' => null, // No end date anymore
            'total_hours' => $validated['total_hours'],
            'work_schedule' => $validated['work_schedule'],
            'signature_type' => $validated['signature_type'],
            'signature_data' => $validated['signature_data'] ?? null,
            'additional_notes' => $validated['additional_notes'] ?? null,
            'letter_path' => $letterPath,
            'document_id' => $documentId,
        ]);

        // Update acceptance request status
        $acceptanceRequest->update(['status' => 'completed']);

        // Create document submission for student
        $requirement = DocumentRequirement::where('name', 'Letter of Acceptance')->first();
        
        if ($requirement) {
            StudentDocumentSubmission::create([
                'student_user_id' => $acceptanceRequest->student_user_id,
                'document_requirement_id' => $requirement->id,
                'file_path' => $letterPath,
                'original_filename' => 'acceptance_letter_' . $documentId . '.pdf',
                'file_size' => Storage::disk('public')->size($letterPath),
                'mime_type' => 'application/pdf',
                'status' => 'submitted',
            ]);
        }

        // Link supervisor to student
        $student = User::find($acceptanceRequest->student_user_id);
        if ($student->studentProfile) {
            $student->studentProfile->update([
                'supervisor_id' => $user->id,
                'company_id' => $user->supervisorProfile->company_id,
            ]);
        }

        // Send notification to student
        $student = $acceptanceRequest->student;
        
        // Send email notification to student
        try {
            $student->notify(new \App\Notifications\AcceptanceLetterGenerated($letter));
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }
        
        // Create in-app notification in custom notifications table for student
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
        
        // Notify coordinator of student's department AND program
        if ($student->studentProfile && $student->studentProfile->department && $student->studentProfile->course) {
            $coordinator = \App\Models\User::whereHas('coordinatorProfile', function($query) use ($student) {
                $query->where('department', $student->studentProfile->department)
                      ->whereHas('program', function($q) use ($student) {
                          $q->where('name', $student->studentProfile->course);
                      });
            })->first();
            
            if ($coordinator) {
                // Send email notification to coordinator
                try {
                    $coordinator->notify(new \App\Notifications\AcceptanceLetterGenerated($letter));
                } catch (\Exception $e) {
                    \Log::error('Failed to send email notification to coordinator: ' . $e->getMessage());
                }
                
                // Create in-app notification for coordinator
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

        return view('supervisor.acceptance.success', compact('letter', 'acceptanceRequest'));
    }

    private function generateAcceptanceLetterPDF($acceptanceRequest, $data, $documentId)
    {
        $student = $acceptanceRequest->student;
        $studentProfile = $student->studentProfile;
        
        // Create PDF using FPDI
        $pdf = new \setasign\Fpdi\Fpdi();
        
        // Check if template exists
        $templatePath = storage_path('app/templates/OJT ACCEPTANCE FORMtemplate.pdf');
        
        if (file_exists($templatePath)) {
            // Use template overlay method
            // 8.5" x 13" = 215.9mm x 330.2mm (Legal/Long bond size)
            $pdf->AddPage('P', [215.9, 330.2]);
            $pdf->setSourceFile($templatePath);
            $tplId = $pdf->importPage(1);
            $pdf->useTemplate($tplId);
            
            // Set font to Arial 13pt (Calibri not available in FPDF, Arial is similar)
            $pdf->SetFont('Arial', '', 13); // Regular weight
            $pdf->SetTextColor(0, 0, 0); // Black text
            
            // Fill in fields - Corrected with actual margins (Left: 0.43", Top: 0.34")
            
            // Date: x=0.57+0.43=1.00" (25.4mm), y=1.49+0.34=1.83" (46.48mm)
            $pdf->SetXY(25.4, 46.48);
            $pdf->Write(0, now()->format('F d, Y'));
            
            // Name: x=0.79+0.43=1.22" (30.99mm), y=2.32+0.34=2.66" (67.56mm)
            $pdf->SetXY(30.99, 67.56);
            $pdf->Write(0, $student->name);
            
            // Program: x=4.92+0.43=5.35" (135.89mm), y=2.67+0.34=3.01" (76.45mm)
            $pdf->SetXY(135.89, 76.45);
            $pdf->Write(0, $studentProfile->course ?? 'Information Technology');
            
            // Company: x=3.16+0.43=3.59" (91.19mm), y=3.14+0.34=3.48" (88.39mm)
            $pdf->SetXY(91.19, 88.39);
            $pdf->Write(0, $acceptanceRequest->company_name);
            
            // Location: x=0.63+0.43=1.06" (26.92mm), y=3.57+0.34=3.91" (99.31mm)
            $pdf->SetXY(26.92, 99.31);
            $pdf->Write(0, Auth::user()->supervisorProfile->company->address ?? '');
            
            // Job Title: x=3.90+0.43=4.33" (109.98mm), y=4.75+0.34=5.09" (129.29mm)
            $pdf->SetXY(109.98, 129.29);
            $pdf->Write(0, $data['job_title']);
            
            // Branch/Department: x=3.90+0.43=4.33" (109.98mm), y=5.07+0.34=5.41" (137.41mm)
            $pdf->SetXY(109.98, 137.41);
            $pdf->Write(0, $data['department'] ?? '');
            
            // Working hours: x=3.90+0.43=4.33" (109.98mm), y=5.39+0.34=5.73" (145.54mm)
            $schedule = $this->formatWorkSchedule($data['work_schedule']);
            $pdf->SetXY(109.98, 145.54);
            $pdf->Write(0, $schedule);
            
            // Total hours: x=3.90+0.43=4.33" (109.98mm), y=6.03+0.34=6.37" (161.80mm)
            $pdf->SetXY(109.98, 161.80);
            $pdf->Write(0, $data['total_hours'] . ' hours');
            
            // Effective Date: x=3.90+0.43=4.33" (109.98mm), y=6.35+0.34=6.69" (169.93mm)
            $pdf->SetXY(109.98, 169.93);
            $effectiveDate = date('M d, Y', strtotime($data['effective_date']));
            $pdf->Write(0, $effectiveDate);
            
            // Bottom section - Company Representative details
            // Company Representative Name (under "Noted by:")
            $pdf->SetXY(30, 222);
            $pdf->Write(0, Auth::user()->name);
            
            // Position
            $pdf->SetXY(30, 252);
            $pdf->Write(0, Auth::user()->supervisorProfile->position ?? '');
            
            // Department
            $pdf->SetXY(30, 277);
            $pdf->Write(0, $data['department'] ?? '');
            
            // Contact
            $pdf->SetXY(30, 295);
            $pdf->Write(0, Auth::user()->email);
            
        } else {
            // Fallback: Generate simple PDF without template
            $pdf->AddPage('P', [215.9, 330.2]);
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'OJT ACCEPTANCE FORM', 0, 1, 'C');
            $pdf->Ln(10);
            
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 8, 'Date: ' . now()->format('F d, Y'), 0, 1);
            $pdf->Ln(5);
            
            $pdf->MultiCell(0, 6, "This is to signify the approval of on-the-job request allowing {$student->name}, a {$studentProfile->course} student, to render practicum at {$acceptanceRequest->company_name}.");
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
            $pdf->Cell(0, 7, Auth::user()->name, 0, 1);
            $pdf->Cell(0, 7, Auth::user()->supervisorProfile->position ?? '', 0, 1);
            $pdf->Cell(0, 7, Auth::user()->email, 0, 1);
        }
        
        // Save PDF
        $filename = 'acceptance_letter_' . $documentId . '.pdf';
        $path = 'acceptance-letters/' . $filename;
        $fullPath = storage_path('app/public/' . $path);
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $pdf->Output('F', $fullPath);
        
        return $path;
    }
    
    private function formatWorkSchedule($schedule)
    {
        $days = [];
        foreach ($schedule as $day => $data) {
            if ($day === 'shift_start' || $day === 'shift_end') {
                continue;
            }
            if (isset($data['enabled']) && $data['enabled']) {
                $days[] = ucfirst($day);
            }
        }
        
        if (empty($days)) {
            return 'Not specified';
        }
        
        $start = $schedule['shift_start'] ?? '08:00';
        $end = $schedule['shift_end'] ?? '17:00';
        
        return implode(', ', $days) . ' (' . $start . ' - ' . $end . ')';
    }

    public function resend($requestId)
    {
        $request = AcceptanceRequest::findOrFail($requestId);
        
        // Create new token and extend expiration
        $request->update([
            'token' => \Str::random(64),
            'expires_at' => now()->addDays(7),
            'status' => 'pending'
        ]);
        
        // Send new email
        try {
            \Mail::to($request->supervisor_email)->send(new \App\Mail\SupervisorAcceptanceInvitation($request));
            $message = 'A new registration link has been sent to your email.';
        } catch (\Exception $e) {
            $message = 'Failed to send email. Please try again later.';
        }
        
        return view('supervisor.acceptance.resent', compact('request', 'message'));
    }

    public function index()
    {
        $supervisor = Auth::user();
        
        // Get all pending requests (ignore expiration for logged-in supervisors)
        $pendingRequests = AcceptanceRequest::where('supervisor_email', $supervisor->email)
            ->where('status', 'pending')
            ->with('student.studentProfile')
            ->latest()
            ->get();
        
        // No expired requests section needed - supervisors can always generate letters once logged in
        $expiredRequests = collect();
        
        // Get generated letters
        $generatedLetters = AcceptanceLetter::where('supervisor_user_id', $supervisor->id)
            ->with('student', 'company')
            ->latest()
            ->paginate(10);
        
        return view('supervisor.acceptance.index', compact('pendingRequests', 'expiredRequests', 'generatedLetters'));
    }

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

}
