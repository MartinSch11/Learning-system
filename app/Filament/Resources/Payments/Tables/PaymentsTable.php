<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // CAMBIO 1: Usamos 'student.name' en vez de 'student_id'
                TextColumn::make('student.name')
                    ->label('Alumno') // Etiqueta bonita para la cabecera
                    ->sortable()
                    ->searchable(), // ¡Vital! Permite buscar pagos escribiendo el nombre del alumno

                // CAMBIO 2: Usamos 'course.name'
                TextColumn::make('course.name')
                    ->label('Curso')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('ARS') // Tip Senior: Esto lo formatea como moneda ($ 25.000,00)
                    ->sortable(),

                TextColumn::make('payment_date')
                    ->label('Fecha')
                    ->date('d/m/Y') // Formato argentino
                    ->sortable(),

                TextColumn::make('method')
                    ->label('Método')
                    ->badge() // Queda lindo como etiqueta de color
                    ->color(fn (string $state): string => match ($state) {
                        'efectivo' => 'success', // Verde
                        'transferencia' => 'info', // Azul
                        'mercadopago' => 'warning', // Amarillo
                        default => 'gray',
                    }),

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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}