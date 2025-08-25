<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // List sessions for a course + create form (teacher/admin)
    public function sessions(Course $course)
    {
        $this->authorizeViewCourse($course);
        $sessions = $course->has('assignments')->get(); // not required, but OK
        $sessions = $course->classSessions()->latest('session_date')->paginate(12);
        return view('attendance.sessions', compact('course','sessions'));
    }

    public function storeSession(Request $request, Course $course)
    {
        if (!in_array(Auth::user()->role, ['admin','teacher'])) abort(403);

        $data = $request->validate([
            'title'        => ['required','max:255'],
            'session_date' => ['required','date'],
            'starts_at'    => ['nullable','date_format:H:i'],
            'ends_at'      => ['nullable','date_format:H:i'],
        ]);

        $course->classSessions()->create($data + [
            'created_by' => Auth::id(),
            'status'     => 'open',
        ]);

        return back()->with('ok', 'Session created.');
    }

    // Teacher view: mark attendance for enrolled students
    public function manage(ClassSession $session)
    {
        $course = $session->course;
        if (!in_array(Auth::user()->role, ['admin','teacher'])) abort(403);
        if (Auth::user()->role === 'teacher' && $course->teacher_id !== Auth::id()) abort(403);

        // enrolled students for this course
        $students = $course->students()->orderBy('name')->get();
        // index existing marks
        $marks = $session->attendances->keyBy('student_id');

        return view('attendance.manage', compact('session','course','students','marks'));
    }

    public function mark(Request $request, ClassSession $session)
    {
        if (!in_array(Auth::user()->role, ['admin','teacher'])) abort(403);

        $data = $request->validate([
            'student_id' => ['required','exists:users,id'],
            'status'     => ['required','in:present,absent,late'],
        ]);

        Attendance::updateOrCreate(
            ['class_session_id' => $session->id, 'student_id' => $data['student_id']],
            ['status' => $data['status'], 'marked_at' => now()]
        );

        return back()->with('ok', 'Saved.');
    }

    private function authorizeViewCourse(Course $course): void
    {
        $user = Auth::user();
        if ($user->role === 'admin') return;
        if ($user->role === 'teacher' && $course->teacher_id === $user->id) return;
        if ($user->role === 'student' && $course->students()->where('users.id',$user->id)->exists()) return;
        abort(403);
    }

    public function storeBulk(Request $request, Course $course)
    {
    if (!in_array(\Auth::user()->role, ['admin','teacher'])) abort(403);

    $data = $request->validate([
        'title'        => ['required','max:255'], // base title
        'start_date'   => ['required','date'],
        'end_date'     => ['required','date','after_or_equal:start_date'],
        'weekdays'     => ['required','array'],   // e.g. ['mon','wed','fri']
        'weekdays.*'   => ['in:mon,tue,wed,thu,fri,sat,sun'],
        'starts_at'    => ['nullable','date_format:H:i'],
        'ends_at'      => ['nullable','date_format:H:i','after_or_equal:starts_at'],
    ]);

    $map = ['sun'=>0,'mon'=>1,'tue'=>2,'wed'=>3,'thu'=>4,'fri'=>5,'sat'=>6];
    $wanted = collect($data['weekdays'])->map(fn($d)=>$map[$d])->all();

    $from = \Carbon\Carbon::parse($data['start_date'])->startOfDay();
    $to   = \Carbon\Carbon::parse($data['end_date'])->startOfDay();

    $cursor = $from->copy();
    while ($cursor->lte($to)) {
        if (in_array($cursor->dayOfWeek, $wanted)) {
            $course->classSessions()->firstOrCreate(
                [
                    'session_date' => $cursor->toDateString(),
                    'starts_at'    => $data['starts_at'] ?? null,
                ],
                [
                    'title'     => $data['title'].' - '.$cursor->toFormattedDateString(),
                    'ends_at'   => $data['ends_at'] ?? null,
                    'created_by'=> \Auth::id(),
                    'status'    => 'open',
                ]
            );
        }
        $cursor->addDay();
    }

    return back()->with('ok', 'Sessions generated.');
    }

}