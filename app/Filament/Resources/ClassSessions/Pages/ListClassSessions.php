<?php

namespace App\Filament\Resources\ClassSessions\Pages;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use App\Models\Course;
use App\Models\ClassSession;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class ListClassSessions extends ListRecords
{
    protected static string $resource = ClassSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Botón de creación manual (por si hay que agregar una clase suelta)
            Actions\CreateAction::make(),

            // ACCIÓN DE GENERACIÓN MASIVA (ADMIN)
            Actions\Action::make('generateSessions')
                ->label(__('Generate Sessions'))
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                // 1. Visible SOLO para Administradores
                ->visible(function () {
                    /** @var User|null $user */
                    $user = Auth::user();
                    return $user && $user->hasRole('admin');
                })
                ->schema([
                    // 2. El Admin puede elegir CUALQUIER curso
                    Select::make('course_id')
                        ->label(__('Course'))
                        ->options(Course::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    DatePicker::make('start_date')
                        ->label(__('Start Date'))
                        ->required()
                        ->default(now()),

                    DatePicker::make('end_date')
                        ->label(__('End Date'))
                        ->required()
                        ->after('start_date')
                        ->default(now()->addMonths(3)),
                ])
               ->action(function (array $data) {
                    $course = Course::find($data['course_id']);

                    // Validaciones básicas
                    if (!$course || !$course->teacher_id) {
                        Notification::make()->title('Error: Invalid course or no teacher assigned')->danger()->send();
                        return;
                    }

                    $schedule = $course->schedule;
                    if (empty($schedule) || !is_array($schedule)) {
                        Notification::make()->title('Error: The course has no schedule defined')->danger()->send();
                        return;
                    }

                    // 1. Aseguramos fechas limpias (Inicio del día 00:00:00)
                    $startDate = Carbon::parse($data['start_date'])->startOfDay();
                    $endDate = Carbon::parse($data['end_date'])->endOfDay(); // Hasta el final del último día
                    
                    $count = 0;

                    // 2. Calculamos cuántos días hay en total
                    $totalDays = $startDate->diffInDays($endDate);

                    // 3. Recorremos día por día usando un FOR simple (Es más robusto que foreach de periodos)
                    for ($i = 0; $i <= $totalDays; $i++) {
                        // Creamos la fecha actual sumando días
                        $date = $startDate->copy()->addDays($i);
                        
                        // Obtenemos el número de día (1=Lunes, 7=Domingo)
                        $currentDayIso = $date->dayOfWeekIso;

                        // Recorremos los horarios del curso
                        foreach ($schedule as $slot) {
                            // Ignoramos slots rotos
                            if (!is_array($slot)) continue;

                            // 4. COMPARACIÓN ESTRICTA: Convertimos ambos a entero para que no falle
                            $slotDay = isset($slot['day_of_week']) ? (int)$slot['day_of_week'] : 0;

                            // Si el día coincide (Ej: Lunes == Lunes)
                            if ($slotDay === $currentDayIso) {
                                try {
                                    // Armamos fecha y hora exacta de inicio
                                    $timeString = $slot['start_time'] ?? $slot['time'] ?? '00:00';
                                    $startDateTime = $date->copy()->setTimeFromTimeString($timeString);

                                    // Armamos hora fin
                                    $endTimeString = $slot['end_time'] ?? null;
                                    $endDateTime = $endTimeString 
                                        ? $date->copy()->setTimeFromTimeString($endTimeString) 
                                        : null;

                                    // 5. CREACIÓN (Evitando duplicados exactos)
                                    ClassSession::firstOrCreate([
                                        'course_id'  => $course->id,
                                        'start_time' => $startDateTime, // Esto incluye Fecha + Hora
                                    ], [
                                        'end_time'   => $endDateTime,
                                        'teacher_id' => $course->teacher_id,
                                        'topic'      => null,
                                    ]);

                                    $count++;
                                } catch (\Exception $e) {
                                    continue; // Si falla algo en la fecha, seguimos
                                }
                            }
                        }
                    }

                    Notification::make()
                        ->title(__(':count sessions generated successfully', ['count' => $count]))
                        ->success()
                        ->send();
                }),

        ];
    }
}
