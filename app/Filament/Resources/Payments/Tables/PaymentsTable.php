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
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('course.name')
                    ->label(__('Course'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('ARS')
                    ->sortable(),

                TextColumn::make('payment_date')
                    ->label(__('Payment Date'))
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('method')
                    ->label(__('Payment Method'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'efectivo' => __('Cash'),
                        'transferencia' => __('Transfer'),
                        default => ucfirst($state), // MercadoPago queda igual
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'efectivo' => 'success',
                        'transferencia' => 'info',
                        'mercadopago' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
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
