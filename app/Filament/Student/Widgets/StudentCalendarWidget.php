<?php

namespace App\Filament\Student\Widgets;

use App\Models\ClassSession;
use App\Models\Exam;
use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StudentCalendarWidget extends Widget
{
    protected string $view = 'filament.student.widgets.student-calendar-widget';
    protected int | string | array $columnSpan = 'full';

    public $weekOffset = 0;
    
    // Variables para el Modal
    public $modalRecord = null; 
    public $modalType = null; // 'class' o 'exam'

    public function getHeading(): string
    {
        return 'Mi Calendario';
    }

    public function getWeekStart(): Carbon
    {
        return now()->startOfWeek()->addWeeks($this->weekOffset);
    }

    public function getWeekEnd(): Carbon
    {
        return $this->getWeekStart()->endOfWeek();
    }

    public function getEventos(): array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$user->studentProfile) return [];

        $start = $this->getWeekStart();
        $end = $this->getWeekEnd();
        $studentId = $user->studentProfile->id;

        $events = [];

        // 1. OBTENER CLASES
        $sessions = ClassSession::query()
            ->whereHas('course.enrollments', function ($q) use ($studentId) {
                $q->where('student_id', $studentId)->where('status', 'cursando');
            })
            ->with(['course', 'teacher'])
            ->whereBetween('start_time', [$start, $end])
            ->get();

        foreach ($sessions as $session) {
            $estado = 'pendiente';
            if ($session->start_time->isPast() && $session->end_time->isFuture()) $estado = 'en_curso';
            elseif ($session->end_time->isPast()) $estado = 'completado';

            $events[] = [
                'id' => $session->id,
                'type' => 'class',
                'title' => $session->course->name,
                'topic' => $session->topic,
                'start' => $session->start_time->toIso8601String(),
                'end' => $session->end_time->toIso8601String(),
                'estado' => $estado,
                'teacher_name' => $session->teacher->name,
            ];
        }

        // 2. OBTENER EXÁMENES
        $exams = Exam::query()
            ->whereHas('course.enrollments', function ($q) use ($studentId) {
                $q->where('student_id', $studentId)->where('status', 'cursando');
            })
            ->with(['course'])
            ->whereBetween('date', [$start, $end])
            ->get();

        foreach ($exams as $exam) {
            // TRUCO: Buscamos si hay clase ese día para robarle el horario
            // Si no hay clase, inventamos un horario (ej: 09:00 a 11:00)
            $classOnExamDay = ClassSession::where('course_id', $exam->course_id)
                ->whereDate('start_time', $exam->date)
                ->first();

            if ($classOnExamDay) {
                $startTime = $classOnExamDay->start_time;
                $endTime = $classOnExamDay->end_time;
            } else {
                // Horario default si es un examen en fecha especial
                $startTime = Carbon::parse($exam->date)->setTime(9, 0);
                $endTime = Carbon::parse($exam->date)->setTime(11, 0);
            }

            $events[] = [
                'id' => $exam->id,
                'type' => 'exam', // <--- ESTO ES CLAVE PARA EL COLOR
                'title' => 'EXAMEN: ' . $exam->title,
                'topic' => $exam->description, // Usamos topic para mostrar descripción
                'start' => $startTime->toIso8601String(),
                'end' => $endTime->toIso8601String(),
                'estado' => 'examen', // Estado especial
                'teacher_name' => 'Evaluación',
            ];
        }

        return $events;
    }

    // Método genérico para abrir cualquier cosa
    public function openDetailModal($id, $type)
    {
        $this->modalType = $type;

        if ($type === 'class') {
            $this->modalRecord = ClassSession::with('course', 'teacher')->find($id);
        } elseif ($type === 'exam') {
            $this->modalRecord = Exam::with('course')->find($id);
        }

        $this->dispatch('open-modal', id: 'detail-modal');
    }
}