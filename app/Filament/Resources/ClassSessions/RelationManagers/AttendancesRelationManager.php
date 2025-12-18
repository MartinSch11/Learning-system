<?php

namespace App\Filament\Resources\ClassSessions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions;
use Filament\Notifications\Notification;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    public static function getTitle($ownerRecord, $pageClass): string
    {
        return __('Student Attendance List');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable(),

                ToggleColumn::make('is_present')
                    ->label(__('Present'))
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->update(['is_justified' => false]);
                        }
                    }),

                ToggleColumn::make('is_justified')
                    ->label(__('Justified'))
                    ->onColor('warning')
                    ->offColor('gray')
                    ->disabled(fn ($record) => $record->is_present),
            ])
            // === ACÁ AGREGAMOS EL BOTÓN DE SINCRONIZAR ===
            ->headerActions([
                Actions\Action::make('sync')
                    ->label(__('Sync Students')) // "Sincronizar Alumnos"
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->action(function () {
                        // 1. Obtenemos la sesión actual (la clase)
                        $session = $this->getOwnerRecord();

                        // 2. Buscamos TODOS los alumnos que deberían estar (Cursando)
                        $currentStudentIds = $session->course->enrollments()
                            ->where('status', 'cursando')
                            ->pluck('student_id');

                        $count = 0;

                        // 3. Recorremos y agregamos los que falten
                        foreach ($currentStudentIds as $studentId) {
                            // firstOrCreate: Si ya existe no hace nada, si no existe lo crea.
                            $attendance = $session->attendances()->firstOrCreate([
                                'student_id' => $studentId,
                            ], [
                                // Valores por defecto para el nuevo
                                'is_present' => false,
                                'is_justified' => false,
                            ]);

                            if ($attendance->wasRecentlyCreated) {
                                $count++;
                            }
                        }

                        // 4. Avisamos qué pasó
                        if ($count > 0) {
                            Notification::make()
                                ->title(__(':count new students added to the list', ['count' => $count]))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('The list is already up to date'))
                                ->info()
                                ->send();
                        }
                    }),
            ])
            ->actions([])
            ->headerActions([]);
    }
}