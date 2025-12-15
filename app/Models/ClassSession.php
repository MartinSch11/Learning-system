<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSession extends Model
{
    protected $fillable = [
        'course_id',
        'date',       // Fecha y hora de la clase
        'topic',      // "Introducción al Álgebra" (Opcional)
        'status',     // 'completed', 'cancelled'
    ];

    // Una sesión pertenece a un curso
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Una sesión tiene muchas asistencias (una por alumno)
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
