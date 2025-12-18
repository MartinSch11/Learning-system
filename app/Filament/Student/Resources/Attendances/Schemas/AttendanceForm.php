<?php

namespace App\Filament\Student\Resources\Attendances\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('class_session_id')
                    ->required()
                    ->numeric(),
                TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                Toggle::make('is_present')
                    ->required(),
                Toggle::make('is_justified')
                    ->required(),
            ]);
    }
}
