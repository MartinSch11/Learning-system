<?php

namespace App\Policies;

use App\Models\ClassSession;
use App\Models\User;

class ClassSessionPolicy
{
    public function viewAny(User $user): bool
    {
        // Admin y Teacher pueden entrar al menú
        return $user->hasRole('admin') || $user->hasRole('teacher');
    }

    public function view(User $user, ClassSession $classSession): bool
    {
        return $user->hasRole('admin') || ($user->hasRole('teacher') && $classSession->teacher_id === $user->id);
    }

    public function create(User $user): bool
    {
        // Solo Admin crea (según definimos antes con el botón masivo)
        return $user->hasRole('admin'); 
    }

    public function update(User $user, ClassSession $classSession): bool
    {
        // Admin siempre puede. Teacher solo si es SU clase.
        if ($user->hasRole('admin')) return true;
        
        return $user->hasRole('teacher') && $classSession->teacher_id === $user->id;
    }

    public function delete(User $user, ClassSession $classSession): bool
    {
        // Solo el Admin borra clases
        return $user->hasRole('admin');
    }
}