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

        // Get user's submissions
        $submissions = StudentDocumentSubmission::where('student_user_id', $user->id)
            ->with(['requirement', 'reviewer'])
            ->get()
            ->keyBy('document_requirement_id');

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
        
        // Get students in coordinator's department
        $students = \App\Models\User::where('role', 'intern')
            ->whereHas('studentProfile', function($query) use ($department) {
                $query->where('department', $department);
            })
            ->with(['studentProfile', 'documentSubmissions.requirement'])
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
                ->first();

            return view('documents.show', compact('requirement', 'submission'));
        }
        
        abort(403);
    }

    public function submit(Request $request, DocumentRequirement $requirement)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403);

        $request->validate([
            'file' => [
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

        // Check if already submitted
        $existingSubmission = StudentDocumentSubmission::where('student_user_id', $user->id)
            ->where('document_requirement_id', $requirement->id)
            ->first();

        if ($existingSubmission) {
            return back()->withErrors(['file' => 'You have already submitted a document for this requirement.']);
        }

        // Store file
        $file = $request->file('file');
        $path = $file->store('document-submissions', 'public');

        // Create submission record
        StudentDocumentSubmission::create([
            'student_user_id' => $user->id,
            'document_requirement_id' => $requirement->id,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document submitted successfully!');
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

    public function review(Request $request, StudentDocumentSubmission $submission)
    {
        $user = Auth::user();
        abort_unless($user->isCoordinator(), 403);

        $request->validate([
            'status' => ['required', 'in:under_review,approved,rejected'],
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
            'message' => 'Your ' . $submission->requirement->name . ' has been ' . $request->status . '.',
            'data' => [
                'submission_id' => $submission->id,
                'status' => $request->status,
            ],
        ]);

        return back()->with('success', 'Document review updated successfully!');
    }
}
