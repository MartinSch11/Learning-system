<?php

namespace App\Filament\Resources\Exams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ExamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.name')->sortable()->searchable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('date')->date('d/m/Y')->sortable(),
                TextColumn::make('grades_count') // Contar cuántos tienen nota
                    ->counts('grades')
                    ->label('Notas cargadas'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
