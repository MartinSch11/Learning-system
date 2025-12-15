<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Course;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PaymentForm
{
    public static function configure($schema) 
    {
        return $schema
            ->schema([
                // 1. ALUMNO (Trigger)
                Select::make('student_id')
                    ->label('Alumno')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live() 
                    // QUITAMOS "Set" antes de $set. Dejamos solo function ($set, $state)
                    ->afterStateUpdated(function ($set, $state) {
                        // Tu lógica futura...
                        /* try {
                             // Lógica de Enrollment...
                        } catch (\Exception $e) {}
                        */
                    }),

                // 2. CURSO (Receptivo y Trigger de Precio)
                Select::make('course_id')
                    ->label('Curso')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live() 
                    // QUITAMOS "Set" antes de $set.
                    ->afterStateUpdated(function ($set, $state) {
                        if (!$state) {
                            $set('amount', null);
                            return;
                        }
                        
                        // Busca el curso en la BD y sacamos el precio
                        $course = Course::find($state);
                        if ($course) {
                            $set('amount', $course->price);
                        }
                    }),

                // 3. MONTO (Resultado)
                TextInput::make('amount')
                    ->label('Monto ($)')
                    ->required()
                    ->numeric()
                    ->prefix('$'),

                // Fecha
                DatePicker::make('payment_date')
                    ->label('Fecha de Pago')
                    ->required()
                    ->default(now()),

                // Método
                Select::make('method')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'mercadopago' => 'MercadoPago'
                    ])
                    ->default('efectivo')
                    ->required(),
            ]);
    }
}