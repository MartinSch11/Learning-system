<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    // ESTO ES LO QUE TE FALTABA:
    protected $fillable = [
        'name',
        'dni',
        'birth_date',
        'phone',
        'medical_notes',
        'active',
    ];

    // Relación: Un alumno tiene muchos pagos
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Relación: Un alumno tiene muchas inscripciones
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Un atajo útil: "Cursos actuales"
    public function currentEnrollments()
    {
        return $this->hasMany(Enrollment::class)->where('status', 'cursando');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Helper opcional para saber si vino o no
    public function attendancePercentage($courseId)
    {
        // Lógica para calcular porcentaje filtrando por las sesiones de ese curso
        // Esto es muy "Senior": encapsular lógica de negocio en el modelo.
    }

    // Quiénes son los padres/tutores de este chico
    public function guardians()
    {
        return $this->belongsToMany(User::class, 'student_user')
            ->withPivot('relationship');
    }

    // Cuál es el usuario de login de este chico
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
