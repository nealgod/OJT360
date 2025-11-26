<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequirement;
use App\Models\StudentDocumentSubmission;
use App\Services\PrePlacementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;

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
        
        // Get all active document requirements ordered by display_order
        $requirements = DocumentRequirement::active()
            ->orderBy('display_order')
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

            // Check if student has submitted required documents (for Letter of Acceptance validation)
            // BOTH Application Letter and PDS/Resume are required
            // Resume Builder and Application Letter Builder are optional (not checked)
            
            // Check for Application Letter (REQUIRED)
            $hasApplicationLetter = StudentDocumentSubmission::where('student_user_id', $user->id)
                ->whereHas('requirement', function($q) {
                    $q->where('name', 'Application Letter')
                      ->where('type', 'pre_placement');
                })
                ->whereIn('status', ['submitted', 'approved', 'rejected'])
                ->exists();
            
            // Check for PDS/Resume (REQUIRED)
            $hasPDS = StudentDocumentSubmission::where('student_user_id', $user->id)
                ->whereHas('requirement', function($q) {
                    $q->where('name', 'LIKE', '%Personal Data Sheet%')
                      ->where('type', 'pre_placement');
                })
                ->whereIn('status', ['submitted', 'approved', 'rejected'])
                ->exists();
            
            // Both Application Letter and PDS/Resume must be submitted
            $hasApplication = $hasApplicationLetter && $hasPDS;
            
            // Get requirement IDs for direct links
            $appLetterReq = \App\Models\DocumentRequirement::where('name', 'Application Letter')
                ->where('type', 'pre_placement')
                ->first();
            $pdsReq = \App\Models\DocumentRequirement::where('name', 'LIKE', '%Personal Data Sheet%')
                ->where('type', 'pre_placement')
                ->first();

            return view('documents.show', compact('requirement', 'submission', 'submissionsAll', 'hasApplication', 'hasApplicationLetter', 'hasPDS', 'appLetterReq', 'pdsReq'));
        }
        
        abort(403);
    }

    public function submit(Request $request, DocumentRequirement $requirement)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403);

        // Get the dynamic max files limit for this requirement
        $maxFiles = $requirement->max_files_per_submission ?? 1;
        
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

            $submission = StudentDocumentSubmission::create([
                'student_user_id' => $user->id,
                'document_requirement_id' => $requirement->id,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
            AuditLog::log(
                'document_submitted',
                'Student submitted document',
                'StudentDocumentSubmission',
                $submission->id,
                null,
                [
                    'requirement_id' => (int) $requirement->id,
                    'requirement_name' => (string) $requirement->name,
                    'file' => (string) $submission->original_filename,
                ]
            );
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

        PrePlacementService::recalculateForStudent($user->id);
        
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

        // Check if this was submitted from student-documents (Resume or Application Letter)
        $requirement = $submission->requirement;
        if ($requirement) {
            // Reset submitted flags for Resume/PDS
            if (stripos($requirement->name, 'Resume') !== false || stripos($requirement->name, 'PDS') !== false) {
                \App\Models\Resume::where('user_id', $user->id)
                    ->where('submitted_to_documents', true)
                    ->update([
                        'submitted_to_documents' => false,
                        'submitted_at' => null,
                    ]);
            }
            
            // Reset submitted flags for Application Letter
            if (stripos($requirement->name, 'Application Letter') !== false) {
                \App\Models\ApplicationLetter::where('user_id', $user->id)
                    ->where('submitted_to_documents', true)
                    ->update([
                        'submitted_to_documents' => false,
                        'submitted_at' => null,
                    ]);
            }
        }

        // Delete the file from storage
        if (Storage::disk('public')->exists($submission->file_path)) {
            Storage::disk('public')->delete($submission->file_path);
        }

        // Delete the submission record
        $old = $submission->toArray();
        $submission->delete();
        AuditLog::log(
            'document_cancelled',
            'Student cancelled document submission',
            'StudentDocumentSubmission',
            $submission->id,
            $old,
            null
        );

        PrePlacementService::recalculateForStudent($user->id);

        return redirect()->route('documents.index')->with('success', 'Document submission cancelled successfully! You can now submit another document.');
    }

    public function download(StudentDocumentSubmission $submission)
    {
        $user = Auth::user();
        
        // Check permissions
        $canDownload = false;
        
        if ($user->isStudent() && $submission->student_user_id === $user->id) {
            $canDownload = true;
        } elseif ($user->isSupervisor()) {
            // Check if this supervisor supervises this student
            $student = \App\Models\User::find($submission->student_user_id);
            if ($student && $student->studentProfile && $student->studentProfile->supervisor_id === $user->id) {
                $canDownload = true;
            }
        } elseif ($user->isCoordinator()) {
            $student = \App\Models\User::find($submission->student_user_id);
            $department = $user->coordinatorProfile?->department;
            if ($student->studentProfile?->department === $department) {
                $canDownload = true;
            }
        }
        
        if (!$canDownload) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($submission->file_path, $submission->original_filename);
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

        $old = $submission->getOriginal();
        $submission->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);
        AuditLog::log(
            'document_reviewed',
            'Coordinator reviewed document',
            'StudentDocumentSubmission',
            $submission->id,
            $old,
            [
                'status' => (string) $submission->status,
            ]
        );

        // If rejected, reset submitted flags so student can resubmit
        if ($request->status === 'rejected') {
            $requirement = $submission->requirement;
            if ($requirement) {
                $studentId = $submission->student_user_id;
                
                // Reset submitted flags for Resume/PDS
                if (stripos($requirement->name, 'Resume') !== false || stripos($requirement->name, 'PDS') !== false) {
                    \App\Models\Resume::where('user_id', $studentId)
                        ->where('submitted_to_documents', true)
                        ->update([
                            'submitted_to_documents' => false,
                            'submitted_at' => null,
                        ]);
                }
                
                // Reset submitted flags for Application Letter
                if (stripos($requirement->name, 'Application Letter') !== false) {
                    \App\Models\ApplicationLetter::where('user_id', $studentId)
                        ->where('submitted_to_documents', true)
                        ->update([
                            'submitted_to_documents' => false,
                            'submitted_at' => null,
                        ]);
                }
            }
        }

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
            PrePlacementService::recalculateForStudent($submission->student_user_id);
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
            $old = $submission->getOriginal();
            $submission->update([
                'status' => $status,
                'feedback' => $feedback,
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);
            AuditLog::log(
                'document_reviewed',
                'Coordinator bulk reviewed document',
                'StudentDocumentSubmission',
                $submission->id,
                $old,
                [
                    'status' => (string) $submission->status,
                ]
            );

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
                PrePlacementService::recalculateForStudent($submission->student_user_id);
            }
        }

        $action = $status === 'approved' ? 'approved' : 'rejected';
        return back()->with('success', "Successfully {$action} {$updatedCount} document(s)!");
    }

}
