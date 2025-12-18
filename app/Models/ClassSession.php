<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    protected $fillable = [
        'course_id',
        'teacher_id',
        'start_time',
        'end_time',
        'topic'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relaciones
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // === AUTOMATIZACIÓN DE LISTA ===
    protected static function booted()
    {
        static::created(function ($session) {
            // 1. Buscamos alumnos inscriptos y cursando este curso
            // Usamos la relación 'enrollments' del modelo Course
            $students = $session->course->enrollments()
                ->where('status', 'cursando')
                ->get()
                ->pluck('student_id');

            // 2. Creamos la asistencia vacía para cada uno
            foreach ($students as $studentId) {
                $session->attendances()->create([
                    'student_id' => $studentId,
                    'is_present' => false,   // Por defecto ausente
                    'is_justified' => false,
                ]);
            }
        });
    }
}
