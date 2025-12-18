<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Carbon\Carbon;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),

                TextColumn::make('teacher.name')
                    ->label(__('Teacher'))
                    ->sortable(),

                TextColumn::make('schedule')
                    ->label(__('Schedule'))
                    ->state(function ($record) {
                        $scheduleData = $record->schedule;

                        if (is_string($scheduleData)) {
                            $scheduleData = json_decode($scheduleData, true);
                        }

                        if (empty($scheduleData) || !is_array($scheduleData)) {
                            return '<span class="text-gray-400 text-xs">Sin horarios</span>';
                        }

                        $days = [
                            1 => __('Monday'),
                            2 => __('Tuesday'),
                            3 => __('Wednesday'),
                            4 => __('Thursday'),
                            5 => __('Friday'),
                            6 => __('Saturday'),
                            7 => __('Sunday'),
                        ];

                        $html = '<div class="flex flex-col gap-1">';

                        foreach ($scheduleData as $slot) {
                            if (!is_array($slot)) {
                                continue;
                            }

                            $rawStart = $slot['start_time'] ?? $slot['time'] ?? '00:00';
                            $rawEnd = $slot['end_time'] ?? null;

                            try {
                                $start = \Carbon\Carbon::parse($rawStart)->format('H:i');
                                $end = $rawEnd ? \Carbon\Carbon::parse($rawEnd)->format('H:i') : '??:??';
                            } catch (\Exception $e) {
                                continue;
                            }

                            $dayIndex = $slot['day_of_week'] ?? 0;
                            $dayName = $days[$dayIndex] ?? 'Día ' . $dayIndex;

                            $html .= "
                <div class='inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'>
                    <span class='font-bold mr-1'>{$dayName}:</span> {$start} - {$end}
                </div>
            ";
                        }

                        $html .= '</div>';
                        return $html;
                    })
                    ->html(),

                TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('ARS')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
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
                BulkActionGroup::make([]),
            ]);
    }
}
