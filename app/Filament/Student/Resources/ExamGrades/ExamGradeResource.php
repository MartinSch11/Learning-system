<?php

namespace App\Filament\Student\Resources\ExamGrades;

use App\Filament\Student\Resources\ExamGrades\Pages\CreateExamGrade;
use App\Filament\Student\Resources\ExamGrades\Pages\EditExamGrade;
use App\Filament\Student\Resources\ExamGrades\Pages\ListExamGrades;
use App\Filament\Student\Resources\ExamGrades\Schemas\ExamGradeForm;
use App\Filament\Student\Resources\ExamGrades\Tables\ExamGradesTable;
use App\Models\ExamGrade;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExamGradeResource extends Resource
{
    protected static ?string $model = ExamGrade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // TRADUCCIONES
    public static function getModelLabel(): string
    {
        return __('Exam');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Exams');
    }
    // FIN TRADUCCIONES

    public static function form(Schema $schema): Schema
    {
        return ExamGradeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamGradesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // FILTRO DE SEGURIDAD
    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->studentProfile) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('student_id', $user->studentProfile->id)
            ->whereNotNull('score'); // Solo mostrar si ya tiene nota cargada
    }

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamGrades::route('/'),
            'create' => CreateExamGrade::route('/create'),
            'edit' => EditExamGrade::route('/{record}/edit'),
        ];
    }
}
