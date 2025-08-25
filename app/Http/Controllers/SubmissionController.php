<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Student submits/updates their work for an assignment.
     */
    public function store(Request $request, Assignment $assignment)
    {
        if (Auth::user()->role !== 'student') abort(403);

        $data = $request->validate([
            'notes' => ['nullable','string','max:2000'],
            'file'  => ['nullable','file','max:10240'], // 10 MB
        ]);

        $now = now();

        // ---- submission window checks (BELONG HERE) ----
        if ($assignment->open_at && $now->lt($assignment->open_at)) {
            return back()->withErrors(['file' => 'Submissions are not open yet.'])->withInput();
        }

        if ($assignment->close_at && $now->gt($assignment->close_at)) {
            $lateOk = $assignment->accept_late &&
                      (!$assignment->late_until || $now->lte($assignment->late_until));
            if (!$lateOk) {
                return back()->withErrors(['file' => 'Submission window is closed.'])->withInput();
            }
        }
        // ------------------------------------------------

        // status based on due_at
        $status = 'submitted';
        if ($assignment->due_at && $now->gt($assignment->due_at)) {
            $status = 'late';
        }

        // find existing submission to handle file replacement
        $existing = Submission::where('assignment_id', $assignment->id)
                    ->where('student_id', Auth::id())
                    ->first();

        $payload = [
            'notes'        => $data['notes'] ?? null,
            'submitted_at' => $now,
            'status'       => $status,
        ];

        if ($request->hasFile('file')) {
            // delete old file if re-uploading
            if ($existing && $existing->file_path) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $payload['file_path'] = $request->file('file')->store('submissions', 'public');
        }

        Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => Auth::id()],
            $payload
        );

        return back()->with('ok', 'Submitted!');
    }

    /**
     * Admin/teacher grades a submission.
     */
    public function grade(Request $request, Submission $submission)
    {
        if (!in_array(Auth::user()->role, ['admin','teacher'])) abort(403);

        $data = $request->validate([
            'grade'  => ['nullable','string','max:20'],
            'status' => ['required','in:submitted,graded,late'],
        ]);

        $submission->update($data);

        return back()->with('ok', 'Graded.');
    }

    /**
     * Download the submitted file (student who owns it, or staff).
     */
    public function download(Submission $submission)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin','teacher']) && $submission->student_id !== $user->id) {
            abort(403);
        }

        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($submission->file_path);
    }
}
