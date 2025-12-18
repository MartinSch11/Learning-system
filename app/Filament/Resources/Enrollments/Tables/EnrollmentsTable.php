<?php

namespace App\Filament\Resources\Enrollments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class EnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('year')
                    ->label(__('Academic Year'))
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cursando' => __('Studying'),
                        'aprobado' => __('Approved'),
                        'inscripto' => __('Enrolled'),
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'cursando' => 'info',
                        'aprobado' => 'success',
                        'inscripto' => 'primary',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'cursando' => __('Studying'),
                        'aprobado' => __('Approved'),
                        'inscripto' => __('Enrolled'),
                    ])
                    ->placeholder(__('All')),
                SelectFilter::make('year')
                    ->label(__('Academic Year'))
                    ->options(fn() => \App\Models\Enrollment::distinct()->pluck('year', 'year')->toArray())
                    ->placeholder(__('All')),

                SelectFilter::make('course_id')
                    ->label(__('Course'))
                    ->options(fn() => \App\Models\Course::pluck('name', 'id')->toArray())
                    ->placeholder(__('All')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // ACCIÓN: PASAR A CURSANDO
                   BulkAction::make('markAsStudying')
                        ->label(__('Start Studying'))
                        ->icon('heroicon-o-book-open')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            // 1. FILTRADO INTELIGENTE
                            // De todos los seleccionados, solo agarramos los que están "inscriptos"
                            // Ignoramos Aprobados, Reprobados o los que ya están Cursando.
                            $count = 0;
                            
                            foreach ($records as $record) {
                                if ($record->status === 'inscripto') {
                                    $record->update(['status' => 'cursando']);
                                    $count++;
                                }
                            }

                            // 2. Notificación con detalle
                            if ($count > 0) {
                                Notification::make()
                                    ->title(__(':count enrollments started studying', ['count' => $count]))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title(__('No enrollments were updated (only "Enrolled" status can be changed)'))
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
