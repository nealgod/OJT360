<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DocumentRequirement;
use App\Models\StudentDocumentSubmission;
use App\Services\PrePlacementService;
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

        if (! $department) {
            return redirect()->route('coord.students.index')->with('error', 'No department assigned to your coordinator profile.');
        }

        // Get students in coordinator's department
        $students = \App\Models\User::where('role', 'intern')
            ->whereHas('studentProfile', function ($query) use ($department) {
                $query->where('department', $department);
            })
            ->with(['studentProfile', 'documentSubmissions' => function ($query) {
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
                ->whereHas('requirement', function ($q) {
                    $q->where('name', 'Application Letter')
                      ->where('type', 'pre_placement');
                })
                ->whereIn('status', ['submitted', 'approved', 'rejected'])
                ->exists();

            // Check for PDS/Resume (REQUIRED)
            $hasPDS = StudentDocumentSubmission::where('student_user_id', $user->id)
                ->whereHas('requirement', function ($q) {
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
                'max:'.$maxFiles,
            ],
            'files.*' => [
                'required',
                'file',
                'max:'.$requirement->max_file_size_mb * 1024, // Convert MB to KB
                function ($attribute, $value, $fail) use ($requirement) {
                    if ($requirement->file_types && ! in_array($value->getClientOriginalExtension(), $requirement->file_types)) {
                        $fail('File type must be one of: '.implode(', ', $requirement->file_types));
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

            AuditLog::log('document_submitted', 'Document submitted', 'StudentDocumentSubmission', $submission->id);
        }

        $fileCount = count($files);
        $message = $fileCount === 1 ? 'Document submitted successfully!' : $fileCount.' documents submitted successfully!';

        // Notify coordinator about new document submission
        $coordinator = \App\Models\User::where('role', 'coordinator')
            ->whereHas('coordinatorProfile', function ($q) use ($user) {
                $q->where('department', $user->studentProfile?->department);
            })
            ->first();

        if ($coordinator) {
            \App\Models\Notification::create([
                'user_id' => $coordinator->id,
                'type' => 'document_submitted',
                'title' => 'New Document Submission',
                'message' => $user->name.' submitted '.($fileCount === 1 ? 'a document' : $fileCount.' documents').' for '.$requirement->name.'.',
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
        abort_unless((int) $submission->student_user_id === (int) $user->id, 403);

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
        $submission->delete();

        PrePlacementService::recalculateForStudent($user->id);

        return redirect()->route('documents.index')->with('success', 'Document submission cancelled successfully! You can now submit another document.');
    }

    public function download(StudentDocumentSubmission $submission)
    {
        $user = Auth::user();

        // Check permissions
        $canDownload = false;

        if ($user->isStudent() && (int) $submission->student_user_id === (int) $user->id) {
            $canDownload = true;
        } elseif ($user->isSupervisor()) {
            // Check if this supervisor supervises this student
            $student = \App\Models\User::find($submission->student_user_id);
            if ($student && $student->studentProfile && (int) $student->studentProfile->supervisor_id === (int) $user->id) {
                $canDownload = true;
            }
        } elseif ($user->isCoordinator()) {
            $student = \App\Models\User::find($submission->student_user_id);
            $department = $user->coordinatorProfile?->department;
            if ($student && $student->studentProfile?->department === $department) {
                $canDownload = true;
            }
        }

        if (! $canDownload) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        try {
            return Storage::disk('public')->download($submission->file_path, $submission->original_filename);
        } catch (\Exception $e) {
            \Log::error('Document download error', [
                'submission_id' => $submission->id,
                'file_path' => $submission->file_path,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Error downloading document');
        }
    }

    public function stream(StudentDocumentSubmission $submission)
    {
        $user = Auth::user();

        if ($user->isStudent() && (int) $submission->student_user_id !== (int) $user->id) {
            abort(403);
        }

        if ($user->isCoordinator()) {
            $student = \App\Models\User::find($submission->student_user_id);
            $department = $user->coordinatorProfile?->department;
            if (!$student || $student->studentProfile?->department !== $department) {
                abort(403);
            }
        }

        if (! Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        try {
            // Get the actual file path
            $filePath = Storage::disk('public')->path($submission->file_path);
            
            // Use stored mime_type or fallback to extension-based detection
            $mime = $submission->mime_type;
            if (!$mime && file_exists($filePath)) {
                $mime = mime_content_type($filePath) ?: 'application/octet-stream';
            }
            if (!$mime) {
                $mime = 'application/octet-stream';
            }

            return response()->file($filePath, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="'.$submission->original_filename.'"',
            ]);
        } catch (\Exception $e) {
            \Log::error('Document stream error', [
                'submission_id' => $submission->id,
                'file_path' => $submission->file_path,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Error loading document');
        }
    }

    // Coordinator document review (approve/reject) has been retired.
    // Coordinators now use the documents dashboard to monitor submissions
    // and missing requirements only; no manual approval flow.
}
