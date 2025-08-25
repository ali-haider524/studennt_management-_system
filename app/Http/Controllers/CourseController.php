<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $courses = Course::with('teacher')->latest()->paginate(12);
        } elseif ($user->role === 'teacher') {
            $courses = Course::with('teacher')->where('teacher_id', $user->id)->latest()->paginate(12);
        } else {
            $courses = Course::with('teacher')->where('status','active')->latest()->paginate(12);
        }

        return view('courses.index', compact('courses','user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => ['required','max:20','unique:courses,code'],
            'title'       => ['required','max:255'],
            'description' => ['nullable','string'],
            'teacher_id'  => ['nullable','exists:users,id'],
            'start_date'  => ['nullable','date'],
            'end_date'    => ['nullable','date','after_or_equal:start_date'],
            'capacity'    => ['nullable','integer','min:1'],
            'status'      => ['required','in:draft,active,completed,archived'],
        ]);

        if (Auth::user()->role === 'teacher') {
            $data['teacher_id'] = Auth::id();
        }

        Course::create($data);

        return back()->with('ok', 'Course created.');
    }

    public function enroll(Course $course)
    {
        $user = Auth::user();

        Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['status'  => 'enrolled']
        );

        return back()->with('ok', 'You are enrolled.');
    }
}
