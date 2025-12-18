<?php

namespace App\Filament\Student\Resources\Attendances\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // 1. ORDEN: Lo más reciente arriba para que vea lo último primero
            ->defaultSort('created_at', 'desc')
            ->columns([
                // COLUMNA 1: Curso y Tema
                TextColumn::make('classSession.course.name')
                    ->label(__('Course'))
                    ->description(fn(Model $record) => $record->classSession->topic ?? '') // Muestra el tema abajo chiquito
                    ->searchable()
                    ->sortable(),

                // COLUMNA 2: Fecha y Hora (Formateada linda)
                TextColumn::make('classSession.start_time')
                    ->label(__('Date & Time'))
                    ->formatStateUsing(function (Model $record) {
                        $start = $record->classSession->start_time;
                        $end = $record->classSession->end_time;

                        // Obtenemos el idioma actual de la app (en, es, etc.)
                        $locale = app()->getLocale();

                        // Formateamos la fecha respetando el idioma
                        // ucfirst para que "friday" sea "Friday"
                        $dateString = ucfirst($start->locale($locale)->isoFormat('ddd D MMM'));

                        return $dateString . ' — ' .
                            $start->format('H:i') . ' a ' . ($end ? $end->format('H:i') : '??');
                    })
                    ->sortable(),

                // COLUMNA 3: Estado (Presente / Ausente / Justificado)
                TextColumn::make('is_present')
                    ->label(__('Status'))
                    ->badge() // Lo hace parecer una etiqueta de color
                    ->formatStateUsing(function (Model $record) {
                        if ($record->is_present) return __('Present');
                        if ($record->is_justified) return __('Justified');
                        return __('Absent');
                    })
                    ->color(function (Model $record) {
                        if ($record->is_present) return 'success';   // Verde
                        if ($record->is_justified) return 'warning'; // Naranja/Amarillo
                        return 'danger';                             // Rojo
                    })
                    ->icon(function (Model $record) {
                        if ($record->is_present) return 'heroicon-m-check-circle';
                        if ($record->is_justified) return 'heroicon-m-shield-check'; // O un reloj
                        return 'heroicon-m-x-circle';
                    }),
            ])
            ->filters([
                // Podrías agregar un filtro por curso acá si querés
            ])
            // IMPORTANTE: Dejamos las acciones vacías para que sea SOLO LECTURA
            ->actions([])
            ->bulkActions([]);
    }
}
