<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('dni')
                    ->required(),
                DatePicker::make('birth_date'),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                Textarea::make('medical_notes')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
