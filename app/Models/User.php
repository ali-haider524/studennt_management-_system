<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // roles we use
    public const ADMIN   = 'admin';
    public const TEACHER = 'teacher';
    public const STUDENT = 'student';
    public const ALUMNI  = 'alumni';

    protected $fillable = [
        'name','email','password','role','phone','is_active',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /** taught courses (if teacher) */
    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /** enrollments (student) */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /** courses via enrollments (student) */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withTimestamps()
            ->withPivot('status','grade');
    }

    /** submissions (student) */
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }
}
