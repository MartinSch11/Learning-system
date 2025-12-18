<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Toggle;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Full Name'))
                    ->searchable(),

                TextColumn::make('dni')
                    ->label(__('National ID'))
                    ->searchable(),

                TextColumn::make('birth_date')
                    ->label(__('Birth Date'))
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('Phone Number'))
                    ->searchable(),

                IconColumn::make('active')
                    ->label(__('Active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course')
                    ->label(__('Filter by Course'))
                    ->schema([
                        Select::make('course_id')
                            ->label(__('Course'))
                            ->options(Course::pluck('name', 'id'))
                            ->searchable()
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['course_id'])) {
                            return $query;
                        }
                        return $query->whereHas('enrollments', function (Builder $q) use ($data) {
                            $q->where('course_id', $data['course_id']);
                        });
                    }),

                TernaryFilter::make('active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueLabel(__('Active Only'))    // Solo los activos
                    ->falseLabel(__('Inactive Only')) // Solo los inactivos
                    ->placeholder(__('All')),         // Todos (borra el filtro)
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([

                    BulkAction::make('promocionar')
                        ->label(__('Enroll in New Cycle'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading(__('Mass Enrollment'))
                        ->schema([
                            // --- SECCIÓN NUEVA INSCRIPCIÓN ---
                            Select::make('course_id')
                                ->label(__('Enroll in Course'))
                                ->options(\App\Models\Course::query()->pluck('name', 'id'))
                                ->required()
                                ->searchable(),

                            TextInput::make('year')
                                ->label(__('Academic Year'))
                                ->numeric()
                                ->default(now()->year + 1)
                                ->required(),

                            // --- SECCIÓN CIERRE DE CURSADA ANTERIOR ---
                            Section::make('Cierre de Cursada Anterior')
                                ->schema([
                                    Toggle::make('close_previous')
                                        ->label('¿Finalizar cursadas anteriores?')
                                        ->default(true)
                                        ->live(), // Importante para que el Select de abajo reaccione

                                    Select::make('previous_status')
                                        ->label('Estado Final')
                                        ->options([
                                            'aprobado' => __('Approved'),   // Pasó de nivel
                                            'reprobado' => __('Failed'),    // Tiene que recursar (en vez de Libre)
                                        ])
                                        ->default('aprobado')
                                        // CORRECCIÓN: La clase Get correcta
                                        ->visible(fn(Get $get) => $get('close_previous'))
                                        ->required(),
                                ])
                                ->compact(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $student) {
                                // 1. PASO PREVIO: Cerrar cursada anterior
                                if ($data['close_previous']) {
                                    $student->enrollments()
                                        ->where('status', 'cursando')
                                        ->where('year', '<', $data['year']) // Solo años viejos
                                        ->update([
                                            'status' => $data['previous_status']
                                        ]);
                                }

                                // 2. PASO ACTUAL: Crear nueva inscripción
                                // firstOrCreate evita duplicados EXACTOS (mismo alumno, curso y año)
                                \App\Models\Enrollment::firstOrCreate([
                                    'student_id' => $student->id,
                                    'course_id'  => $data['course_id'],
                                    'year'       => $data['year'],
                                ], [
                                    'status' => 'inscripto', // Arranca como inscripto
                                ]);
                            }

                            Notification::make()
                                ->title(__('Enrollments created successfully'))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
