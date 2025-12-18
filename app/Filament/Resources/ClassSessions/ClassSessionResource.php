<?php

namespace App\Filament\Resources\ClassSessions;

use App\Filament\Resources\ClassSessions\Pages\CreateClassSession;
use App\Filament\Resources\ClassSessions\Pages\EditClassSession;
use App\Filament\Resources\ClassSessions\Pages\ListClassSessions;
use App\Filament\Resources\ClassSessions\Schemas\ClassSessionForm;
use App\Filament\Resources\ClassSessions\Tables\ClassSessionsTable;
use App\Models\ClassSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class ClassSessionResource extends Resource
{
    protected static ?string $model = ClassSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    protected static ?int $navigationSort = 2; 

    // TRADUCCIONES
    public static function getModelLabel(): string
    {
        return __('Class Session');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Class Sessions');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Si es Teacher (y no es Admin)
        if ($user && ! $user->hasRole('admin')) {
            $query->where('teacher_id', $user->id)
                // Filtro: Fecha de inicio menor o igual a "Hoy a las 23:59:59"
                ->where('start_time', '<=', now()->endOfDay());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return ClassSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // ACÁ VAMOS A PONER EL GESTOR DE ASISTENCIA
            RelationManagers\AttendancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassSessions::route('/'),
            'create' => CreateClassSession::route('/create'),
            'edit' => EditClassSession::route('/{record}/edit'),
        ];
    }
}
