<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Crear Usuario
        $user = \App\Models\User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            // Si no puso pass, usamos el DNI
            'password' => \Illuminate\Support\Facades\Hash::make($data['password'] ?? $data['dni']),
        ]);

        $user->assignRole('student'); // O 'parent', como prefieras llamarle al rol

        // 2. Limpiar datos que no van en la tabla students
        unset($data['email']);
        unset($data['password']);

        // 3. Vincular y Crear Alumno
        $data['user_id'] = $user->id;

        return static::getModel()::create($data);
    }
}
