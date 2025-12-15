<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'amount',
        'payment_date',
        'method',
    ];

    // Relación: Este pago pertenece a un Alumno
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // Relación: Este pago pertenece a un Curso
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
