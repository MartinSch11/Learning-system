<?php

namespace App\Filament\Student\Resources\Attendances;

use App\Filament\Student\Resources\Attendances\Pages\CreateAttendance;
use App\Filament\Student\Resources\Attendances\Pages\EditAttendance;
use App\Filament\Student\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Student\Resources\Attendances\Schemas\AttendanceForm;
use App\Filament\Student\Resources\Attendances\Tables\AttendancesTable;
use App\Models\Attendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        // TRADUCCIONES
    public static function getModelLabel(): string
    {
        return __('Attendance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Attendances');
    }
    // FIN TRADUCCIONES

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->studentProfile) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('student_id', $user->studentProfile->id)
            // FILTRO NUEVO: Solo mostrar asistencias de clases que ya empezaron (o pasaron)
            ->whereHas('classSession', function ($query) {
                $query->where('start_time', '<=', now());
            });
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }


    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
            // 'create' => CreateAttendance::route('/create'),
            // 'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}
