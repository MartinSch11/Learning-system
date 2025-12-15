<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'class_session_id',
        'student_id',
        'status', // 'present', 'absent', 'late', 'excused'
        'notes',  // "Se retiró antes", "Trajo certificado médico"
    ];

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}