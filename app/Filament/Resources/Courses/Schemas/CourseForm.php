<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name')) // Agregado
                    ->required(),

                TextInput::make('schedule')
                    ->label(__('Schedule')) // Agregado
                    ->default(null),

                TextInput::make('price')
                    ->label(__('Price')) // Agregado
                    ->required()
                    ->numeric()
                    ->prefix('$'),
            ]);
    }
}