<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Course Information'))
                    ->description(__('Basic details about the course'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required()
                                    ->placeholder(__('e.g., Kids 1 (Inicial)')),

                                Select::make('teacher_id')
                                    ->label(__('Teacher'))
                                    ->options(\App\Models\User::role('teacher')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                TextInput::make('price')
                                    ->label(__('Price'))
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('0.00'),
                            ]),
                    ])->columnSpanFull(),

                Section::make(__('Weekly Schedule'))
                    ->description(__('Define the days and times for this course'))
                    ->schema([
                        Repeater::make('schedule')
                            ->label(__('Schedule'))
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Select::make('day_of_week')
                                            ->label(__('Day'))
                                            ->options([
                                                1 => __('Monday'),
                                                2 => __('Tuesday'),
                                                3 => __('Wednesday'),
                                                4 => __('Thursday'),
                                                5 => __('Friday'),
                                                6 => __('Saturday'),
                                                7 => __('Sunday'),
                                            ])
                                            ->required(),

                                        TimePicker::make('start_time')
                                            ->label(__('Start Time'))
                                            ->required()
                                            ->seconds(false),

                                        TimePicker::make('end_time')
                                            ->label(__('End Time'))
                                            ->required()
                                            ->seconds(false)
                                            ->after('start_time'),
                                    ]),
                            ])
                            ->addActionLabel(__('Add Schedule'))
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                isset($state['day_of_week']) 
                                    ? match($state['day_of_week']) {
                                        1 => __('Monday'),
                                        2 => __('Tuesday'),
                                        3 => __('Wednesday'),
                                        4 => __('Thursday'),
                                        5 => __('Friday'),
                                        6 => __('Saturday'),
                                        7 => __('Sunday'),
                                        default => __('New Schedule'),
                                    } . ' - ' . ($state['start_time'] ?? '--:--') . ' a ' . ($state['end_time'] ?? '--:--')
                                    : __('New Schedule')
                            )
                            ->defaultItems(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}