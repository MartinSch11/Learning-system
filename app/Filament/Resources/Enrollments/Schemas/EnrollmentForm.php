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
                    ->label(__('Student'))
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('course_id')
                    ->label(__('Course'))
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('year')
                    ->label(__('Academic Year')) // "Año" queda mejor como "Ciclo Lectivo"
                    ->numeric()
                    ->default(date('Y'))
                    ->required(),

                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'cursando' => __('Studying'),
                        'aprobado' => __('Approved'),
                        'libre' => __('Regularity Lost'), // "Libre" en inglés técnico
                    ])
                    ->default('cursando')
                    ->required(),
            ]);
    }
}
