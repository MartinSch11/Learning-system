<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('dni')
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course')
                    ->label('Filtrar por Curso')
                    ->schema([
                        Select::make('course_id') // Usamos un Select manual
                            ->label('Curso')
                            ->options(Course::pluck('name', 'id'))
                            ->searchable()
                    ])
                    ->query(function (Builder $query, array $data) {
                        // Si no eligió nada, no filtramos
                        if (empty($data['course_id'])) {
                            return $query;
                        }

                        // MAGIA: Filtramos alumnos que tengan (whereHas) una inscripción en ese curso
                        return $query->whereHas('enrollments', function (Builder $q) use ($data) {
                            $q->where('course_id', $data['course_id']);
                            // Opcional: ->where('status', 'cursando'); si solo querés los actuales
                        });
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('promocionar')
                        ->label('Inscribir en Nuevo Ciclo')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Inscripción Masiva')
                        ->schema([
                            Select::make('course_id')
                                ->label('Inscribir en Curso')
                                ->options(Course::query()->pluck('name', 'id'))
                                ->required(),

                            TextInput::make('year')
                                ->label('Ciclo Lectivo')
                                ->numeric()
                                ->default(now()->year + 1)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $student) {
                                Enrollment::create([
                                    'student_id' => $student->id,
                                    'course_id' => $data['course_id'],
                                    'year' => $data['year'],
                                    'status' => 'cursando',
                                ]);
                            }

                            Notification::make()
                                ->title('Inscripciones realizadas con éxito')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
