<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación para saber si este usuario es el login de un alumno
    public function studentProfile()
    {
        return $this->hasOne(Student::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // 1. Si intenta entrar al panel 'admin'
        if ($panel->getId() === 'admin') {
            // Solo admins y teachers pasan
            return $this->hasRole(['admin', 'teacher']);
        }

        // 2. Si intenta entrar al panel 'student'
        if ($panel->getId() === 'student') {
            // Solo students (y si querés que el admin pueda chusmear, agregalo)
            return $this->hasRole(['student', 'admin']);
        }

        return false;
    }
}
