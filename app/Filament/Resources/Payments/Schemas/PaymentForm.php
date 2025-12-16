<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Course;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PaymentForm
{
    public static function configure($schema)
    {
        return $schema
            ->schema([
                // 1. ALUMNO
                Select::make('student_id')
                    ->label(__('Student')) // <--- Key
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($set, $state) {
                        // Tu lógica...
                    }),

                // 2. CURSO
                Select::make('course_id')
                    ->label(__('Course')) // <--- Key
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($set, $state) {
                        if (!$state) {
                            $set('amount', null);
                            return;
                        }
                        $course = Course::find($state);
                        if ($course) {
                            $set('amount', $course->price);
                        }
                    }),

                // 3. MONTO
                TextInput::make('amount')
                    ->label(__('Amount')) // <--- Key
                    ->required()
                    ->numeric()
                    ->prefix('$'),

                // Fecha
                DatePicker::make('payment_date')
                    ->label(__('Payment Date')) // <--- Key
                    ->required()
                    ->default(now()),

                // Método
                Select::make('method')
                    ->label(__('Payment Method')) // <--- Key
                    ->options([
                        'efectivo' => __('Cash'),           // <--- Traducción de valores
                        'transferencia' => __('Transfer'),
                        'mercadopago' => 'MercadoPago'      // Nombre propio, no se traduce
                    ])
                    ->default('efectivo')
                    ->required(),
            ]);
    }
}
