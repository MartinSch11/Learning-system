<?php

namespace App\Filament\Student\Resources\Payments;

use App\Filament\Student\Resources\Payments\Pages\CreatePayment;
use App\Filament\Student\Resources\Payments\Pages\EditPayment;
use App\Filament\Student\Resources\Payments\Pages\ListPayments;
use App\Filament\Student\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Student\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
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

        // Verificamos que exista el usuario y que tenga perfil de alumno
        if (! $user || ! $user->studentProfile) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('student_id', $user->studentProfile->id);
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
            'index' => ListPayments::route('/'),
            // 'create' => CreatePayment::route('/create'),
            // 'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
