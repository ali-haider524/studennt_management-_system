<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AttendanceController;


Route::get('/', fn () => view('welcome'));

Route::view('/admin', 'admin.dashboard')
    ->middleware(['auth','role:admin'])
    ->name('admin.dashboard');

Route::middleware(['auth', 'role:teacher'])
    ->get('/teacher', fn () => view('teacher.dashboard'))
    ->name('teacher.dashboard');

Route::middleware(['auth', 'role:student,alumni'])
    ->get('/student', fn () => view('student.dashboard'))
    ->name('student.dashboard');

// Optional: redirect the generic /dashboard to the right page by role
Route::get('/dashboard', function () {
    $u = auth()->user();
    return match ($u->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'teacher' => redirect()->route('teacher.dashboard'),
        default   => redirect()->route('student.dashboard'),
    };
})->middleware('auth')->name('dashboard');

Route::get('/__check_views', function () {
    return [
        'exists' => view()->exists('admin.dashboard'),
        'paths'  => config('view.paths'),
        'files'  => collect(glob(resource_path('views/admin/*')))->map(fn($p)=>str_replace(base_path().DIRECTORY_SEPARATOR,'',$p)),
    ];
});

Route::middleware(['auth'])->group(function () {
    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])
        ->middleware('role:admin,teacher')->name('courses.store');
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])
        ->middleware('role:student')->name('courses.enroll');

    // Assignments
    Route::get('/courses/{course}/assignments', [AssignmentController::class, 'index'])
        ->name('assignments.index');
    Route::post('/courses/{course}/assignments', [AssignmentController::class, 'store'])
        ->middleware('role:admin,teacher')->name('assignments.store');

    // Submissions
    Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'store'])
        ->middleware('role:student')->name('submissions.store');
    Route::post('/submissions/{submission}/grade', [SubmissionController::class, 'grade'])
        ->middleware('role:admin,teacher')->name('submissions.grade');
    Route::get('/submissions/{submission}/download', [SubmissionController::class, 'download'])
        ->name('submissions.download');

    Route::get('/courses/{course}/attendance', [AttendanceController::class, 'sessions'])
        ->name('attendance.sessions');

    Route::post('/courses/{course}/attendance/sessions', [AttendanceController::class, 'storeSession'])
        ->middleware('role:admin,teacher')->name('attendance.sessions.store');

    Route::get('/attendance/sessions/{session}', [AttendanceController::class, 'manage'])
        ->middleware('role:admin,teacher')->name('attendance.manage');

    Route::post('/attendance/sessions/{session}/mark', [AttendanceController::class, 'mark'])
        ->middleware('role:admin,teacher')->name('attendance.mark');

    Route::post('/courses/{course}/attendance/sessions/bulk', [AttendanceController::class, 'storeBulk'])
        ->middleware('role:admin,teacher')
        ->name('attendance.sessions.bulk');

});

// Optional: let /course redirect to /courses so both work
Route::redirect('/course', '/courses');

require __DIR__ . '/auth.php';
