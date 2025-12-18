<?php

namespace App\Filament\Student\Resources\ExamGrades\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Models\ExamGrade;

class ExamGradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exam.course.name')
                    ->label(__('Course'))
                    ->sortable(),

                TextColumn::make('exam.title')
                    ->label(__('Exam'))
                    ->description(fn(ExamGrade $record) => $record->exam->date->format('d/m/Y'))
                    ->searchable(),

                TextColumn::make('score')
                    ->label(__('Score'))
                    ->badge() // Toma el color del Enum (getColor) automáticamente
                    ->sortable(),

                TextColumn::make('feedback')
                    ->label(__('Comments'))
                    ->wrap()
                    ->limit(50),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
