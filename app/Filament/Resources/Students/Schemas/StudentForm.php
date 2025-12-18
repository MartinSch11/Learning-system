<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                // SECCIÓN 1
                Section::make(__('Student Information'))
                    ->icon('heroicon-m-academic-cap')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Full Name'))
                            ->required(),

                        TextInput::make('dni')
                            ->label(__('National ID'))
                            ->required()
                            ->numeric()
                            ->unique(ignoreRecord: true),

                        DatePicker::make('birth_date')
                            ->label(__('Birth Date'))
                            ->required(),

                        Toggle::make('active')
                            ->label(__('Active'))
                            ->required()
                            ->onColor('success'),
                    ])
                    ->collapsible(),

                // SECCIÓN 2
                Section::make(__('Emergency Contact'))
                    ->icon('heroicon-m-phone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('parent_name')
                            ->label(__('Parent/Guardian Name'))
                            ->required(),

                        TextInput::make('phone')
                            ->label(__('Phone Number'))
                            ->tel()
                            ->required(),
                    ])
                    ->collapsed(),

                // SECCIÓN 3
                Section::make(__('System Access Data'))
                    ->description(__('These credentials will be used for login.'))
                    ->icon('heroicon-m-key')
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->unique(table: 'users', column: 'email', ignoreRecord: true)
                            ->dehydrated(false),

                        TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->required()
                            ->dehydrated(false)
                            ->helperText(__('If left empty, password will be the National ID.')),
                    ])
                    ->collapsed(),
            ]);
    }
}
