<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dni',
        'birth_date',
        'phone',
        'parent_name',
        'medical_notes',
        'active',
    ];

    // Relación: Un alumno pertenece a un Usuario (Login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- Relaciones Académicas ---

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function currentEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->where('status', 'cursando');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
