<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'class_session_id',
        'student_id',
        'is_present',
        'is_justified'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }
}
