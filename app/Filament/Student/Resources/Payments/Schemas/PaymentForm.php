<?php

namespace App\Filament\Student\Resources\Payments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'name')
                    ->default(null),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('payment_date')
                    ->required(),
                Select::make('method')
                    ->options(['efectivo' => 'Efectivo', 'transferencia' => 'Transferencia', 'mercadopago' => 'Mercadopago'])
                    ->default('efectivo')
                    ->required(),
            ]);
    }
}
