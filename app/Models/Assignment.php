<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id','title','description','due_at','created_by','visibility'
    ];


    public function course()  { return $this->belongsTo(Course::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function submissions() { return $this->hasMany(Submission::class); }

    protected $casts = [
    'due_at'     => 'datetime',
    'open_at'    => 'datetime',
    'close_at'   => 'datetime',
    'late_until' => 'datetime',
    ];

}
