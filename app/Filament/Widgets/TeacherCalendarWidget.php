<?php

namespace App\Filament\Widgets;

use App\Models\ClassSession;
use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TeacherCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.teacher-calendar-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public int $weekOffset = 0;
    public ?ClassSession $record = null;

    public function getHeading(): string
    {
        return __('My Class Schedule');
    }

    public function getWeekStart(): Carbon
    {
        return now()->startOfWeek()->addWeeks($this->weekOffset);
    }

    public function getWeekEnd(): Carbon
    {
        return $this->getWeekStart()->endOfWeek();
    }

    /**
     * Mejorado el mapeo de eventos para clases
     */
    public function getEventos(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) return [];

        $start = $this->getWeekStart();
        $end = $this->getWeekEnd();

        $query = ClassSession::query()
            ->with(['course', 'teacher'])
            ->whereBetween('start_time', [$start, $end])
            ->orderBy('start_time');

        // Admin ve todo, Teacher solo lo suyo
        if (!$user->hasRole('admin')) {
            $query->where('teacher_id', $user->id);
        }

        $sessions = $query->get();

        return $sessions->map(function ($session) {
            $estado = 'pendiente';
            if ($session->start_time->isPast() && $session->end_time->isFuture()) {
                $estado = 'en_curso';
            } elseif ($session->end_time->isPast()) {
                $estado = 'completado';
            }

            return [
                'id' => $session->id,
                'title' => $session->course->name ?? 'Sin curso',
                'topic' => $session->topic,
                'start' => $session->start_time->toIso8601String(),
                'end' => $session->end_time->toIso8601String(),
                'estado' => $estado,
                'teacher_name' => $session->teacher->name ?? 'Sin asignar',
                'start_time_formatted' => $session->start_time->format('H:i'),
                'end_time_formatted' => $session->end_time->format('H:i'),
                'date_formatted' => $session->start_time->locale('es')->isoFormat('dddd D [de] MMMM'),
            ];
        })->toArray();
    }

    public function getEventosPorDia(): array
    {
        $eventos = $this->getEventos();
        $porDia = [];

        foreach ($eventos as $evento) {
            $fecha = Carbon::parse($evento['start'])->format('Y-m-d');
            if (!isset($porDia[$fecha])) {
                $porDia[$fecha] = [];
            }
            $porDia[$fecha][] = $evento;
        }

        // Ordenar por hora dentro de cada día
        foreach ($porDia as &$eventosDelDia) {
            usort(
                $eventosDelDia,
                fn($a, $b) =>
                Carbon::parse($a['start'])->timestamp - Carbon::parse($b['start'])->timestamp
            );
        }

        return $porDia;
    }

    public function openReservaModal($id): void
    {
        $this->record = ClassSession::with(['course', 'teacher'])->find($id);
        $this->dispatch('open-modal', id: 'reserva-modal');
    }

    public function irATomarAsistencia()
    {
        if ($this->record) {
            return redirect()->to('/admin/class-sessions/' . $this->record->id . '/edit');
        }
    }

    public static function canView(): bool
    {
        /** @var \App\Models\User|null $user */  // <--- ESTA LÍNEA ES LA SOLUCIÓN
        $user = Auth::user();
        return $user && ($user->hasRole('teacher') || $user->hasRole('admin'));
    }
}
