<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash; // <--- Importante para encriptar

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),

                TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->required(),

                DateTimePicker::make('email_verified_at')
                    ->label(__('Verified At')),

                // ACÁ ESTÁ LA MAGIA
                TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    // 1. Encriptar la contraseña antes de guardar
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    // 2. Solo guardar si el usuario escribió algo (filled)
                    ->dehydrated(fn($state) => filled($state))
                    // 3. Requerido SOLO si la operación es 'create'
                    ->required(fn(string $operation): bool => $operation === 'create'),

                Select::make('roles')
                    ->label(__('Role'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
