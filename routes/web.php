<?php

use Illuminate\Support\Facades\Route;

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


require __DIR__ . '/auth.php';
