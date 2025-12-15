<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Alumno')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('course_id')
                    ->label('Curso')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('year')
                    ->label('Año')
                    ->numeric()
                    ->default(date('Y'))
                    ->required(),

                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'cursando' => 'Cursando',
                        'aprobado' => 'Aprobado',
                        'libre' => 'Libre',
                    ])
                    ->default('cursando')
                    ->required(),
            ]);
    }
}
