<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','title','description','teacher_id','start_date','end_date','capacity','status'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withTimestamps()
            ->withPivot('status','grade');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function classSessions()
    {
    return $this->hasMany(\App\Models\ClassSession::class);
    }

}
