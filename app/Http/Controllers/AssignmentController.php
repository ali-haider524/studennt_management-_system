<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        $user = Auth::user();
        $q = $course->assignments()->with('creator')->latest();
        if (!in_array($user->role, ['admin','teacher'])) {
            $q->whereIn('visibility', ['published','closed']);
        }
        $assignments = $q->paginate(12);

        return view('assignments.index', compact('course','assignments'));
    }

    public function store(Request $request, Course $course)
    {
        if (!in_array(Auth::user()->role, ['admin','teacher'])) abort(403);

       $data = $request->validate([
            'title'       => ['required','max:255'],
            'description' => ['nullable','string'],
            'open_at'     => ['nullable','date'],
            'due_at'      => ['nullable','date','after_or_equal:open_at'],
            'close_at'    => ['nullable','date','after_or_equal:due_at'],
            'accept_late' => ['sometimes','boolean'],
            'late_until'  => ['nullable','date','after:close_at'],
            'visibility'  => ['required','in:draft,published,closed'],
        ]);

       Assignment::create([
            'course_id'   => $course->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'open_at'     => $data['open_at'] ?? null,
            'due_at'      => $data['due_at'] ?? null,
            'close_at'    => $data['close_at'] ?? null,
            'accept_late' => (bool)($data['accept_late'] ?? false),
            'late_until'  => $data['late_until'] ?? null,
            'visibility'  => $data['visibility'],
            'created_by'  => \Auth::id(),
]);


        return back()->with('ok', 'Assignment created.');
    }
}
