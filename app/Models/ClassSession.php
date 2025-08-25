<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id','title','session_date','starts_at','ends_at','created_by','status'
    ];

    public function course()  { return $this->belongsTo(Course::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function attendances() { return $this->hasMany(Attendance::class); }
}
