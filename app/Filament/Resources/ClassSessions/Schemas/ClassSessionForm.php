<?php

namespace App\Filament\Resources\ClassSessions\Schemas;

use App\Models\Course;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class ClassSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label(__('Course'))
                    ->relationship('course', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->disabled(fn(string $operation): bool => $operation === 'edit') // No cambiar curso al editar
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $course = Course::find($state);
                            if ($course && $course->teacher_id) {
                                $set('teacher_id', $course->teacher_id);
                            }
                        }
                    })
                    ->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        DateTimePicker::make('start_time')
                            ->label(__('Start Time'))
                            ->required()
                            ->default(now())
                            ->seconds(false)
                            // === VALIDACIÓN DE DÍAS PERMITIDOS ===
                            ->rule(function (Get $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $courseId = $get('course_id');
                                    
                                    // Si no hay curso seleccionado, no validamos nada (ya saltará el required del curso)
                                    if (!$courseId) return;

                                    $course = Course::find($courseId);
                                    if (!$course || empty($course->schedule)) return;

                                    // Obtenemos el día de la semana de la fecha seleccionada (1=Lunes, 7=Domingo)
                                    $selectedDayIso = Carbon::parse($value)->dayOfWeekIso;

                                    // Extraemos los días permitidos del schedule
                                    // schedule es un array de objetos: [{"day_of_week": 1, ...}, ...]
                                    $allowedDays = collect($course->schedule)
                                        ->pluck('day_of_week')
                                        ->map(fn($day) => (int)$day) // Aseguramos que sean enteros
                                        ->toArray();

                                    if (!in_array($selectedDayIso, $allowedDays)) {
                                        $fail(__('This course only has classes on: :days', [
                                            'days' => self::getDayNames($allowedDays)
                                        ]));
                                    }
                                };
                            }),

                        DateTimePicker::make('end_time')
                            ->label(__('End Time'))
                            ->required()
                            ->after('start_time')
                            ->seconds(false),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('topic')
                            ->label(__('Topic'))
                            ->maxLength(255),

                        Select::make('teacher_id')
                            ->label(__('Teacher'))
                            ->relationship('teacher', 'name')
                            ->required()
                            ->searchable()
                            ->dehydrated(),
                    ]),
            ]);
    }

    // Helper para mostrar nombres de días en el error
    protected static function getDayNames(array $days): string
    {
        $map = [
            1 => __('Monday'), 2 => __('Tuesday'), 3 => __('Wednesday'),
            4 => __('Thursday'), 5 => __('Friday'), 6 => __('Saturday'), 7 => __('Sunday'),
        ];

        return collect($days)->map(fn($d) => $map[$d] ?? '')->filter()->join(', ');
    }
}