<?php

namespace App\Http\Controllers;

use App\Imports\WhitelistRowsImport;
use App\Models\EnrollmentWhitelist;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CoordinatorImportController extends Controller
{
    public function showImport()
    {
        $coordinator = Auth::user();
        $program = $coordinator->coordinatorProfile?->program;
        if (! $program) {
            return redirect()->route('coord.students.index')->with('error', 'No program assigned to you.');
        }

        return view('coord.students.import', compact('program'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        $uploaded = $request->file('file');
        $ext = strtolower($uploaded->getClientOriginalExtension());
        $rows = [];

        try {
            if (in_array($ext, ['xlsx', 'xls'])) {
                $sheets = Excel::toArray(new WhitelistRowsImport, $uploaded);
                $rows = $sheets[0] ?? [];
            } else {
                // CSV/TXT fallback
                $path = $uploaded->getRealPath();
                if (($handle = fopen($path, 'r')) !== false) {
                    $header = fgetcsv($handle);
                    if (! $header) {
                        return back()->withErrors(['file' => 'Uploaded CSV is empty or missing a header row.']);
                    }
                    $normalized = array_map(fn ($h) => strtolower(trim((string) $h)), $header);
                    while (($data = fgetcsv($handle)) !== false) {
                        // skip blank lines
                        if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                            continue;
                        }
                        if (count($data) !== count($normalized)) {
                            // pad or trim to match header length
                            $data = array_pad($data, count($normalized), null);
                            $data = array_slice($data, 0, count($normalized));
                        }
                        $assoc = [];
                        foreach ($normalized as $i => $key) {
                            $val = isset($data[$i]) ? trim((string) $data[$i]) : null;
                            if ($val) {
                                // Fix for special characters (like Ñ)
                                $val = mb_convert_encoding($val, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                            }
                            $assoc[$key] = $val;
                        }
                        $rows[] = $assoc;
                    }
                    fclose($handle);
                }
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Could not read the file. Please ensure it is a valid CSV/XLSX.'])->withInput();
        }

        $coordinator = Auth::user();
        $program = $coordinator->coordinatorProfile?->program;
        if (! $program) {
            return back()->withErrors(['file' => 'No program assigned to your coordinator profile.']);
        }

        $results = [
            'valid' => [],
            'invalid' => [],
            'meta' => [
                'total_rows' => 0,
                'processed_rows' => 0,
                'duplicate_ids' => 0,
                'duplicate_emails' => 0,
            ],
        ];

        // Helper to map external headers to internal keys
        $mapExternalRow = function (array $row) {
            $lower = [];
            foreach ($row as $k => $v) {
                $lower[strtolower(trim($k))] = trim((string) $v);
            }
            // Support both our original template and external export
            $studentId = $lower['student_id'] ?? $lower['student id'] ?? null;
            $studentNameRaw = $lower['name'] ?? $lower['student name'] ?? null;
            $email = $lower['email'] ?? $lower['e-mail'] ?? null;
            $phone = $lower['contact_number'] ?? $lower['phone'] ?? null;
            // Parse name if in "Last, First Middle" format
            $name = null;
            if ($studentNameRaw) {
                $parts = array_map('trim', explode(',', $studentNameRaw));
                if (count($parts) >= 2) {
                    $last = $parts[0];
                    $firstMiddle = trim($parts[1]);
                    $name = $last.', '.preg_replace('/\s+/', ' ', $firstMiddle);
                } else {
                    $name = preg_replace('/\s+/', ' ', $studentNameRaw);
                }
            }

            return [
                'student_id' => $studentId,
                'name' => $name,
                'email' => $email,
                'contact_number' => $phone,
            ];
        };

        // Track duplicates within the same file
        $seenIds = [];
        $seenEmails = [];

        foreach ($rows as $i => $row) {
            $normalized = $mapExternalRow($row);
            $errors = [];
            $results['meta']['total_rows']++;
            if (empty($normalized['student_id'])) {
                $errors[] = 'Student ID required';
            }
            if (empty($normalized['name'])) {
                $errors[] = 'Student Name required';
            }
            if (empty($normalized['email'])) {
                $errors[] = 'E-Mail required';
            }
            // if (! str_ends_with(strtolower($normalized['email'] ?? ''), '@evsu.edu.ph')) {
            //     $errors[] = 'E-Mail must be @evsu.edu.ph';
            // }

            $exists = EnrollmentWhitelist::where('student_id', $normalized['student_id'] ?? '')->exists();
            if ($exists) {
                $errors[] = 'Student ID already exists';
            }

            // duplicates within file
            if (! empty($normalized['student_id'])) {
                if (isset($seenIds[$normalized['student_id']])) {
                    $errors[] = 'Duplicate Student ID in file';
                } else {
                    $seenIds[$normalized['student_id']] = true;
                }
            }
            if (! empty($normalized['email'])) {
                if (isset($seenEmails[strtolower($normalized['email'])])) {
                    $errors[] = 'Duplicate Email in file';
                } else {
                    $seenEmails[strtolower($normalized['email'])] = true;
                }
            }

            if ($errors) {
                $results['invalid'][] = ['row' => $row, 'errors' => $errors, 'line' => $i + 2];
            } else {
                $results['valid'][] = [
                    'student_id' => $normalized['student_id'],
                    'name' => $normalized['name'],
                    'contact_number' => $normalized['contact_number'] ?? null,
                    'program_id' => $program->id,
                    'email' => $normalized['email'],
                ];
                $results['meta']['processed_rows']++;
            }
        }

        // If coordinator chose to upload immediately, skip preview
        if ($request->has('import_now')) {
            // Persist original uploaded file per program (single latest file)
            $originalExt = $uploaded->getClientOriginalExtension();
            $storePath = 'whitelists/program_'.$program->id.'/latest.'.$originalExt;
            Storage::disk('local')->put($storePath, file_get_contents($uploaded->getRealPath()));

            foreach ($results['valid'] as $row) {
                EnrollmentWhitelist::firstOrCreate(
                    ['student_id' => $row['student_id']],
                    [
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'program_id' => $row['program_id'],
                        'contact_number' => $row['contact_number'] ?? null,
                        'status' => 'pending',
                    ]
                );
            }

            $imported = count($results['valid']);
            $invalid = count($results['invalid']);

            return redirect()->route('coord.students.whitelist')->with('success', "Imported {$imported} row(s). {$invalid} row(s) were invalid.");
        }

        return view('coord.students.import-preview', compact('results'));
    }

    public function commit(Request $request)
    {
        // Support JSON payload or array
        $rowsPayload = $request->input('rows');
        if (is_string($rowsPayload)) {
            $rowsPayload = json_decode($rowsPayload, true);
        }

        // If decoding failed or empty
        if (! is_array($rowsPayload) || empty($rowsPayload)) {
            return redirect()->route('coord.students.import')
                ->with('error', 'Import failed: No valid data to commit. Please try uploading the file again.');
        }

        $request->merge(['rows' => $rowsPayload]);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.student_id' => ['required', 'string'],
            'rows.*.name' => ['required', 'string'],
            'rows.*.email' => ['required', 'email'],
            'rows.*.program_id' => ['required', 'integer'],
            'rows.*.contact_number' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('coord.students.import')
                ->withErrors($validator)
                ->with('error', 'Validation failed during commit.');
        }

        $validated = $validator->validated();

        // Add new students without removing existing pending ones
        foreach ($validated['rows'] as $row) {
            EnrollmentWhitelist::firstOrCreate(
                ['student_id' => $row['student_id']],
                [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'program_id' => $row['program_id'],
                    'contact_number' => $row['contact_number'] ?? null,
                    'status' => 'pending',
                ]
            );
        }

        return redirect()->route('coord.students.whitelist')->with('success', 'Whitelist imported successfully.');
    }

    public function status(Request $request)
    {
        $coordinator = Auth::user();
        $program = $coordinator->coordinatorProfile?->program;
        if (! $program) {
            return redirect()->route('coord.students.index')->with('error', 'No program assigned to you.');
        }

        $search = trim((string) $request->get('q'));
        $status = $request->get('status');
        $includeArchived = $request->boolean('show_archived', false);

        $query = EnrollmentWhitelist::where('program_id', $program->id)
            ->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%$search%")
                  ->orWhere('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if (in_array($status, ['pending', 'activated'], true)) {
            $query->where('status', $status);
        } else {
            // Default hide archived, unless explicitly requested
            if (! $includeArchived) {
                $query->whereIn('status', ['pending', 'activated']);
            }
        }

        $whitelist = $query->paginate(20)->withQueryString();

        return view('coord.students.whitelist', compact('whitelist', 'search', 'status', 'program', 'includeArchived'));
    }

    public function export(Request $request)
    {
        $coordinator = Auth::user();
        $program = $coordinator->coordinatorProfile?->program;
        if (! $program) {
            return redirect()->route('coord.students.index')->with('error', 'No program assigned to you.');
        }

        $rows = EnrollmentWhitelist::where('program_id', $program->id)
            ->whereIn('status', ['pending', 'activated'])
            ->orderBy('student_id')
            ->get(['student_id', 'name', 'email', 'contact_number', 'status']);

        $filename = 'whitelist_'.strtolower(preg_replace('/\s+/', '_', $program->name)).'_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student ID', 'Student Name', 'E-Mail', 'Phone', 'Status']);
            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->student_id,
                    $r->name,
                    $r->email,
                    $r->contact_number,
                    $r->status,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadUploaded(Request $request)
    {
        $coordinator = Auth::user();
        $program = $coordinator->coordinatorProfile?->program;
        if (! $program) {
            return redirect()->route('coord.students.index')->with('error', 'No program assigned to you.');
        }

        // Try common extensions in priority order
        $base = 'whitelists/program_'.$program->id.'/latest';
        $candidates = [$base.'.xlsx', $base.'.xls', $base.'.csv', $base.'.txt'];
        $found = null;
        foreach ($candidates as $p) {
            if (Storage::disk('local')->exists($p)) {
                $found = $p;
                break;
            }
        }
        if (! $found) {
            return back()->with('error', 'No uploaded class list file found. Please upload a file first.');
        }

        $filename = basename($found);

        return response()->streamDownload(function () use ($found) {
            echo Storage::disk('local')->get($found);
        }, $filename);
    }

    public function endTerm(Request $request)
    {
        $coordinator = Auth::user();
        $program = $coordinator->coordinatorProfile?->program;
        if (! $program) {
            return redirect()->route('coord.students.index')->with('error', 'No program assigned to you.');
        }

        $count = EnrollmentWhitelist::where('program_id', $program->id)
            ->whereIn('status', ['pending', 'activated'])
            ->update(['status' => 'archived']);

        return redirect()->route('coord.students.whitelist')->with('success', "Archived {$count} record(s) for end of term.");
    }
}
