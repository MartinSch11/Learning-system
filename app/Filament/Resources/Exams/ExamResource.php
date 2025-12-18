<?php

namespace App\Filament\Resources\Exams;

use App\Filament\Resources\Exams\Pages\CreateExam;
use App\Filament\Resources\Exams\Pages\EditExam;
use App\Filament\Resources\Exams\Pages\ListExams;
use App\Filament\Resources\Exams\Schemas\ExamForm;
use App\Filament\Resources\Exams\Tables\ExamsTable;
use App\Models\Exam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    public static function getNavigationGroup(): ?string
    {
        return __('Academic'); 
    }
    
    protected static ?int $navigationSort = 3;

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
        return ExamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\GradesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Admin ve todo, Teacher solo lo suyo (a través de los cursos)
        if ($user && !$user->hasRole('admin')) {
            return parent::getEloquentQuery()->whereHas('course', function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            });
        }

        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExams::route('/'),
            'create' => CreateExam::route('/create'),
            'edit' => EditExam::route('/{record}/edit'),
        ];
    }
}
