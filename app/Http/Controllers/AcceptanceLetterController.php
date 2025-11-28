<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceLetter;
use Illuminate\Support\Facades\Storage;

class AcceptanceLetterController extends Controller
{
    public function download(AcceptanceLetter $letter)
    {
        // Check authorization
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        // Allow student, supervisor, or coordinator to download
        if ((int) $user->id !== (int) $letter->student_user_id &&
            (int) $user->id !== (int) $letter->supervisor_user_id &&
            ! $user->isCoordinator()) {
            abort(403, 'Unauthorized');
        }

        // Check if file exists
        if (! Storage::disk('public')->exists($letter->letter_path)) {
            abort(404, 'File not found');
        }

        $filename = 'acceptance_letter_'.$letter->document_id.'.pdf';

        return Storage::disk('public')->download($letter->letter_path, $filename);
    }
}
