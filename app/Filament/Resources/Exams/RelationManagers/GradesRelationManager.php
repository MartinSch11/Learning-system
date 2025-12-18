<?php

namespace App\Filament\Resources\Exams\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SelectColumn;
use App\Enums\ExamGrade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GradesRelationManager extends RelationManager
{
    protected static string $relationship = 'grades';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Grades');
    }
    
    public function isReadOnly(): bool
    {
        return false; // Aseguramos que se pueda editar
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('score')
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable(),

                SelectColumn::make('score')
                    ->label(__('Score'))
                    ->options(ExamGrade::class) // Carga las opciones del Enum
                    ->sortable(),

                TextInputColumn::make('feedback')
                    ->label(__('Feedback'))
                    ->placeholder(__('Very good work...')),
            ])
            ->headerActions([
                // ACCIÓN MAGICA: Traer a todos los alumnos del curso
                Actions\Action::make('syncStudents')
                    ->label(__('Sync Students')) // "Sincronizar Alumnos"
                    ->icon('heroicon-o-arrow-path')
                    ->action(function () {
                        // $this->getOwnerRecord() es el Examen
                        $exam = $this->getOwnerRecord();
                        $course = $exam->course;

                        // Buscamos inscripciones activas
                        $students = $course->enrollments()
                            ->where('status', 'cursando') // Solo los que cursan
                            ->with('student')
                            ->get()
                            ->pluck('student');

                        $count = 0;
                        foreach ($students as $student) {
                            // Creamos la nota vacía si no existe
                            $exam->grades()->firstOrCreate([
                                'student_id' => $student->id
                            ]);
                            $count++;
                        }

                        Notification::make()
                            ->title('Lista de alumnos actualizada')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([
            ]);
    }
}
