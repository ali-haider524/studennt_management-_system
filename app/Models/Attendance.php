<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['class_session_id','student_id','status','marked_at'];

    public function session() { return $this->belongsTo(ClassSession::class, 'class_session_id'); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
}
