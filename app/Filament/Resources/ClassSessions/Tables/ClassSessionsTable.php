<?php

namespace App\Filament\Resources\ClassSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label(__('Date & Time')) // "Fecha y Hora"
                    ->formatStateUsing(function ($record) {

                        $dia = $record->start_time->format('d/m/Y');
                        $inicio = $record->start_time->format('H:i');

                        // Validamos si tiene hora de fin
                        $fin = $record->end_time ? $record->end_time->format('H:i') : '??:??';

                        return "{$dia} {$inicio} - {$fin}";
                    })
                    ->sortable(),

                TextColumn::make('teacher.name')
                    ->label(__('Teacher'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('start_time', 'desc') // Las más recientes primero
            ->filters([
                SelectFilter::make('teacher')
                    ->label(__('Teacher'))
                    ->relationship('teacher', 'name', fn(Builder $query) => $query->role('teacher')),
                SelectFilter::make('course')
                    ->label(__('Course'))
                    ->relationship('course', 'name'),
            ])
            ->recordActions([
                EditAction::make()->label(__('Take Attendance')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
