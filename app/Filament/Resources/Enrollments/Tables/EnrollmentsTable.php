<?php

namespace App\Filament\Resources\Enrollments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class EnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Usamos la notación de punto para acceder a la relación
                TextColumn::make('student.name')
                    ->label('Alumno')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course.name')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('year')
                    ->label('Ciclo Lectivo')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge() // Queda lindo visualmente
                    ->color(fn(string $state): string => match ($state) {
                        'cursando' => 'info',
                        'aprobado' => 'success',
                        'libre' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
