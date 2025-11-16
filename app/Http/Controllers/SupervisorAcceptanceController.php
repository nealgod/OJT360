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

        // Check if expired
        if ($request->expires_at->isPast() || $request->status !== 'pending') {
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
                'contact_person' => $acceptanceRequest->supervisor_name,
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

        // Redirect to acceptance letter form
        return redirect()->route('supervisor.acceptance.create', $token);
    }



    public function create($token)
    {
        $acceptanceRequest = AcceptanceRequest::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with('student.studentProfile')
            ->firstOrFail();

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
        
        // Send email notification
        $student->notify(new \App\Notifications\AcceptanceLetterGenerated($letter));
        
        // Create in-app notification in custom notifications table
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
                'action_url' => route('documents.index'),
                'action_text' => 'View Documents',
            ],
            'read' => false,
        ]);

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
            $pdf->AddPage('P', [215.9, 330.2]); // Long bond in mm
            $pdf->setSourceFile($templatePath);
            $tplId = $pdf->importPage(1);
            $pdf->useTemplate($tplId);
            
            // Set font
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            
            // Fill in fields (coordinates need adjustment based on actual template)
            // DATE
            $pdf->SetXY(140, 50);
            $pdf->Write(0, now()->format('F d, Y'));
            
            // Student Name
            $pdf->SetXY(30, 75);
            $pdf->Write(0, $student->name);
            
            // Course
            $pdf->SetXY(30, 82);
            $pdf->Write(0, $studentProfile->course ?? 'Information Technology');
            
            // Company Name
            $pdf->SetXY(30, 95);
            $pdf->Write(0, $acceptanceRequest->company_name);
            
            // Company Location
            $pdf->SetXY(30, 102);
            $pdf->Write(0, Auth::user()->supervisorProfile->company->address ?? '');
            
            // Job Title
            $pdf->SetXY(120, 120);
            $pdf->Write(0, $data['job_title']);
            
            // Department
            $pdf->SetXY(120, 130);
            $pdf->Write(0, $data['department'] ?? '');
            
            // Supervisor
            $pdf->SetXY(120, 140);
            $pdf->Write(0, $data['immediate_supervisor']);
            
            // Work Schedule
            $schedule = $this->formatWorkSchedule($data['work_schedule']);
            $pdf->SetXY(120, 150);
            $pdf->Write(0, $schedule);
            
            // Total Hours
            $pdf->SetXY(120, 160);
            $pdf->Write(0, $data['total_hours'] . ' hours');
            
            // Effective Date
            $pdf->SetXY(120, 170);
            $effectiveDate = date('M d, Y', strtotime($data['effective_date']));
            $pdf->Write(0, $effectiveDate);
            
            // Company Representative Name
            $pdf->SetXY(30, 220);
            $pdf->Write(0, Auth::user()->name);
            
            // Position
            $pdf->SetXY(30, 235);
            $pdf->Write(0, Auth::user()->supervisorProfile->position ?? '');
            
            // Department
            $pdf->SetXY(30, 245);
            $pdf->Write(0, $data['department'] ?? '');
            
            // Contact
            $pdf->SetXY(30, 255);
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
        
        // Get pending requests
        $pendingRequests = AcceptanceRequest::where('supervisor_email', $supervisor->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with('student.studentProfile')
            ->latest()
            ->get();
        
        // Get generated letters
        $generatedLetters = AcceptanceLetter::where('supervisor_user_id', $supervisor->id)
            ->with('student', 'company')
            ->latest()
            ->paginate(10);
        
        return view('supervisor.acceptance.index', compact('pendingRequests', 'generatedLetters'));
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
