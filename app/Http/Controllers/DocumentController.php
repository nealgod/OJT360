<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequirement;
use App\Models\StudentDocumentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            return $this->studentIndex();
        } elseif ($user->isCoordinator()) {
            return $this->coordinatorIndex();
        }
        
        abort(403);
    }

    private function studentIndex()
    {
        $user = Auth::user();
        
        // Get all active document requirements
        $requirements = DocumentRequirement::active()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // Get user's submissions (grouped per requirement to support up to 2 files)
        $submissions = StudentDocumentSubmission::where('student_user_id', $user->id)
            ->with(['requirement', 'reviewer'])
            ->get()
            ->groupBy('document_requirement_id');

        // Group requirements by type
        $prePlacement = $requirements->where('type', 'pre_placement');
        $postPlacement = $requirements->where('type', 'post_placement');
        $ongoing = $requirements->where('type', 'ongoing');

        return view('documents.index', compact('prePlacement', 'postPlacement', 'ongoing', 'submissions'));
    }

    private function coordinatorIndex()
    {
        $user = Auth::user();
        $department = $user->coordinatorProfile?->department;
        
        if (!$department) {
            return redirect()->route('coord.students.index')->with('error', 'No department assigned to your coordinator profile.');
        }
        
        // Get students in coordinator's department
        $students = \App\Models\User::where('role', 'intern')
            ->whereHas('studentProfile', function($query) use ($department) {
                $query->where('department', $department);
            })
            ->with(['studentProfile', 'documentSubmissions' => function($query) {
                $query->with('requirement')->orderBy('created_at', 'desc');
            }])
            ->get();

        // Get all document requirements
        $requirements = DocumentRequirement::active()->get();

        return view('documents.coordinator', compact('students', 'requirements'));
    }

    public function show(DocumentRequirement $requirement)
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            $submission = StudentDocumentSubmission::where('student_user_id', $user->id)
                ->where('document_requirement_id', $requirement->id)
                ->with('reviewer')
                ->latest()
                ->first();

            $submissionsAll = StudentDocumentSubmission::where('student_user_id', $user->id)
                ->where('document_requirement_id', $requirement->id)
                ->with('reviewer')
                ->orderByDesc('created_at')
                ->get();

            return view('documents.show', compact('requirement', 'submission', 'submissionsAll'));
        }
        
        abort(403);
    }

    public function submit(Request $request, DocumentRequirement $requirement)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403);

        // Get the dynamic max files limit for this requirement
        $maxFiles = $requirement->max_files_per_submission ?? 2;
        
        $request->validate([
            'files' => [
                'required',
                'array',
                'min:1',
                'max:' . $maxFiles,
            ],
            'files.*' => [
                'required',
                'file',
                'max:' . $requirement->max_file_size_mb * 1024, // Convert MB to KB
                function ($attribute, $value, $fail) use ($requirement) {
                    if ($requirement->file_types && !in_array($value->getClientOriginalExtension(), $requirement->file_types)) {
                        $fail('File type must be one of: ' . implode(', ', $requirement->file_types));
                    }
                },
            ],
        ]);

        // Check against the dynamic limit (ignore previously rejected submissions to allow resubmission)
        $existingCount = StudentDocumentSubmission::where('student_user_id', $user->id)
            ->where('document_requirement_id', $requirement->id)
            ->where('status', '!=', 'rejected')
            ->count();
        $newFilesCount = count($request->file('files'));
        
        if ($existingCount + $newFilesCount > $maxFiles) {
            $remaining = $maxFiles - $existingCount;
            return back()->withErrors(['files' => "You can only have a maximum of {$maxFiles} files for this requirement. You currently have {$existingCount} files. You can add {$remaining} more."]);
        }

        // Store files and create submission records
        $files = $request->file('files');
        foreach ($files as $file) {
            $path = $file->store('document-submissions', 'public');

            StudentDocumentSubmission::create([
                'student_user_id' => $user->id,
                'document_requirement_id' => $requirement->id,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        $fileCount = count($files);
        $message = $fileCount === 1 ? 'Document submitted successfully!' : $fileCount . ' documents submitted successfully!';
        
        // Notify coordinator about new document submission
        $coordinator = \App\Models\User::where('role', 'coordinator')
            ->whereHas('coordinatorProfile', function($q) use ($user) {
                $q->where('department', $user->studentProfile?->department);
            })
            ->first();

        if ($coordinator) {
            \App\Models\Notification::create([
                'user_id' => $coordinator->id,
                'type' => 'document_submitted',
                'title' => 'New Document Submission',
                'message' => $user->name . ' submitted ' . ($fileCount === 1 ? 'a document' : $fileCount . ' documents') . ' for ' . $requirement->name . '.',
                'data' => [
                    'submission_count' => $fileCount,
                    'requirement_id' => $requirement->id,
                    'requirement_name' => $requirement->name,
                    'student_user_id' => $user->id,
                ],
            ]);
        }
        
        return redirect()->route('documents.index')->with('success', $message);
    }

    public function cancel(StudentDocumentSubmission $submission)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403);
        abort_unless($submission->student_user_id === $user->id, 403);

        // Only allow cancellation if status is 'submitted' (not reviewed yet)
        if ($submission->status !== 'submitted') {
            return back()->withErrors(['submission' => 'Cannot cancel submission that has already been reviewed.']);
        }

        // Delete the file from storage
        if (Storage::disk('public')->exists($submission->file_path)) {
            Storage::disk('public')->delete($submission->file_path);
        }

        // Delete the submission record
        $submission->delete();

        return redirect()->route('documents.index')->with('success', 'Document submission cancelled successfully!');
    }

    public function download(StudentDocumentSubmission $submission)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->isStudent() && $submission->student_user_id !== $user->id) {
            abort(403);
        }
        
        if ($user->isCoordinator()) {
            $student = \App\Models\User::find($submission->student_user_id);
            $department = $user->coordinatorProfile?->department;
            if ($student->studentProfile?->department !== $department) {
                abort(403);
            }
        }

        if (!Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($submission->file_path, $submission->original_filename);
    }

    public function preview(StudentDocumentSubmission $submission)
    {
        $user = Auth::user();

        if ($user->isStudent() && $submission->student_user_id !== $user->id) {
            abort(403);
        }

        if ($user->isCoordinator()) {
            $student = \App\Models\User::find($submission->student_user_id);
            $department = $user->coordinatorProfile?->department;
            if ($student->studentProfile?->department !== $department) {
                abort(403);
            }
        }

        if (!Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        // Return the file directly for inline viewing (like Google Classroom)
        $mime = $submission->mime_type ?: Storage::disk('public')->mimeType($submission->file_path);
        
        return Storage::disk('public')->response($submission->file_path, $submission->original_filename, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline'
        ]);
    }

    public function stream(StudentDocumentSubmission $submission)
    {
        $user = Auth::user();

        if ($user->isStudent() && $submission->student_user_id !== $user->id) {
            abort(403);
        }

        if ($user->isCoordinator()) {
            $student = \App\Models\User::find($submission->student_user_id);
            $department = $user->coordinatorProfile?->department;
            if ($student->studentProfile?->department !== $department) {
                abort(403);
            }
        }

        if (!Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        $relative = str_starts_with($submission->file_path, 'public/') ? substr($submission->file_path, 7) : $submission->file_path;
        $absolute = storage_path('app/public/' . $relative);
        $mime = $submission->mime_type ?: mime_content_type($absolute);

        return response()->file($absolute, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $submission->original_filename . '"'
        ]);
    }

    public function review(Request $request, StudentDocumentSubmission $submission)
    {
        $user = Auth::user();
        abort_unless($user->isCoordinator(), 403);

        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        // Notify student
        \App\Models\Notification::create([
            'user_id' => $submission->student_user_id,
            'type' => 'document_reviewed',
            'title' => 'Document Review Update',
            'message' => 'Your ' . ($submission->requirement?->name ?? 'document') . ' has been ' . $request->status . '.',
            'data' => [
                'submission_id' => $submission->id,
                'status' => $request->status,
            ],
        ]);

        // Check if all pre-placement requirements are now approved
        if ($request->status === 'approved' && $submission->requirement?->type === 'pre_placement') {
            $this->checkPrePlacementCompletion($submission->student_user_id);
        }

        return back()->with('success', 'Document review updated successfully!');
    }

    public function bulkReview(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isCoordinator(), 403);

        $request->validate([
            'submission_ids' => ['required', 'array', 'min:1'],
            'submission_ids.*' => ['exists:student_document_submissions,id'],
            'status' => ['required', 'in:approved,rejected'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $submissionIds = $request->submission_ids;
        $status = $request->status;
        $feedback = $request->feedback;

        // Get submissions and verify they belong to coordinator's department
        $submissions = StudentDocumentSubmission::whereIn('id', $submissionIds)
            ->with(['student', 'requirement'])
            ->get();

        $department = $user->coordinatorProfile?->department;
        $validSubmissions = $submissions->filter(function($submission) use ($department) {
            return $submission->student->studentProfile?->department === $department;
        });

        if ($validSubmissions->isEmpty()) {
            return back()->withErrors(['error' => 'No valid submissions found for your department.']);
        }

        // Update all valid submissions
        $updatedCount = 0;
        foreach ($validSubmissions as $submission) {
            $submission->update([
                'status' => $status,
                'feedback' => $feedback,
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);

            // Notify student
            \App\Models\Notification::create([
                'user_id' => $submission->student_user_id,
                'type' => 'document_reviewed',
                'title' => 'Document Review Update',
                'message' => 'Your ' . ($submission->requirement?->name ?? 'document') . ' has been ' . $status . '.',
                'data' => [
                    'submission_id' => $submission->id,
                    'status' => $status,
                ],
            ]);

            $updatedCount++;

            // Check pre-placement completion for each approved pre-placement document
            if ($status === 'approved' && $submission->requirement?->type === 'pre_placement') {
                $this->checkPrePlacementCompletion($submission->student_user_id);
            }
        }

        $action = $status === 'approved' ? 'approved' : 'rejected';
        return back()->with('success', "Successfully {$action} {$updatedCount} document(s)!");
    }

    /**
     * Check if all pre-placement requirements are completed and notify student
     */
    private function checkPrePlacementCompletion($studentId)
    {
        // Get all pre-placement requirements
        $prePlacementRequirements = \App\Models\DocumentRequirement::where('type', 'pre_placement')
            ->where('is_required', true)
            ->get();

        if ($prePlacementRequirements->isEmpty()) {
            return;
        }

        // Get all approved pre-placement submissions for this student
        $approvedSubmissions = \App\Models\StudentDocumentSubmission::where('student_user_id', $studentId)
            ->whereIn('document_requirement_id', $prePlacementRequirements->pluck('id'))
            ->where('status', 'approved')
            ->get();

        // Check if all required pre-placement requirements are approved
        $approvedRequirementIds = $approvedSubmissions->pluck('document_requirement_id')->toArray();
        $allRequiredApproved = $prePlacementRequirements->every(function ($requirement) use ($approvedRequirementIds) {
            return in_array($requirement->id, $approvedRequirementIds);
        });

        if ($allRequiredApproved) {
            // Send special notification for pre-placement completion
            \App\Models\Notification::create([
                'user_id' => $studentId,
                'type' => 'pre_placement_complete',
                'title' => '🎉 Pre-Placement Requirements Complete!',
                'message' => 'Congratulations! All your pre-placement requirements have been approved. You can now proceed with your placement request.',
                'data' => [
                    'type' => 'pre_placement_complete',
                    'completed_at' => now()->toISOString(),
                ],
            ]);
        }
    }
}
