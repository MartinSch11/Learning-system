<x-filament::widget>
    <x-filament::section>
        <style>
            /* Clases para alternar vistas (Móvil vs Desktop) */
            .calendar-mobile-view {
                display: block !important;
            }

            .calendar-desktop-view {
                display: none !important;
            }

            @media (min-width: 768px) {
                .calendar-mobile-view {
                    display: none !important;
                }

                .calendar-desktop-view {
                    display: block !important;
                }
            }

            /* Clase auxiliar para flex en móvil */
            .calendar-mobile-flex {
                display: flex !important;
            }

            @media (min-width: 768px) {
                .calendar-mobile-flex {
                    display: none !important;
                }
            }
        </style>

        <div style="display: flex; flex-direction: column; gap: 1rem;">

            {{-- TOOLBAR SUPERIOR --}}
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">

                {{-- GRUPO IZQUIERDO: Botones + Título Móvil --}}
                <div style="display: flex; align-items: center; gap: 1rem;">

                    {{-- Controles (Botones) --}}
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
                        style="display: flex; align-items: center; padding: 0.25rem;">

                        <button wire:click="$set('weekOffset', {{ $weekOffset - 1 }})"
                            class="hover:bg-white dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400"
                            style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: transparent; cursor: pointer;">
                            <div style="width: 20px; height: 20px;">
                                <x-heroicon-m-chevron-left style="width: 100%; height: 100%;" />
                            </div>
                        </button>

                        <button type="button" wire:click="$set('weekOffset', 0)"
                            class="{{ $weekOffset === 0 ? 'bg-white dark:bg-gray-600 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-600 dark:text-gray-300' }}"
                            style="padding: 0.375rem 1rem; margin: 0 0.25rem; font-size: 0.875rem; font-weight: 600; border-radius: 0.375rem; border: none; cursor: pointer;">
                            {{-- TRADUCCIÓN: 'Hoy' o 'Today' --}}
                            {{ __('Today') }}
                        </button>

                        <button wire:click="$set('weekOffset', {{ $weekOffset + 1 }})"
                            class="hover:bg-white dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400"
                            style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: transparent; cursor: pointer;">
                            <div style="width: 20px; height: 20px;">
                                <x-heroicon-m-chevron-right style="width: 100%; height: 100%;" />
                            </div>
                        </button>
                    </div>

                    {{-- Título del Mes (SOLO MÓVIL) --}}
                    <div class="calendar-mobile-flex text-gray-900 dark:text-white"
                        style="font-size: 1.1rem; font-weight: 700; text-transform: capitalize; align-items: center; gap: 0.5rem;">

                        <div style="width: 20px; height: 20px; color: var(--primary-500);">
                            <x-heroicon-o-calendar-days style="width: 100%; height: 100%;" />
                        </div>

                        {{-- TRADUCCIÓN DINÁMICA DEL MES --}}
                        <span>{{ $this->getWeekStart()->locale(app()->getLocale())->isoFormat('MMMM YYYY') }}</span>
                    </div>

                </div>

                {{-- Título del Mes (SOLO DESKTOP) --}}
                <div class="calendar-desktop-view text-gray-900 dark:text-white">
                    <div style="font-size: 1.25rem; font-weight: 700; text-transform: capitalize; display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 24px; height: 24px; color: var(--primary-500);">
                            <x-heroicon-o-calendar-days style="width: 100%; height: 100%;" />
                        </div>
                        {{-- TRADUCCIÓN DINÁMICA DEL MES --}}
                        <span>{{ $this->getWeekStart()->locale(app()->getLocale())->isoFormat('MMMM YYYY') }}</span>
                    </div>
                </div>
            </div>

            {{-- ================================================================================= --}}
            {{-- VISTA MÓVIL (LISTA) --}}
            {{-- ================================================================================= --}}
            <div class="calendar-mobile-view">
                @php
                $weekStart = $this->getWeekStart()->startOfDay();
                $eventsByDay = array_fill(0, 7, []);

                foreach ($this->getEventos() as $evento) {
                $eventDate = \Carbon\Carbon::parse($evento['start'])->startOfDay();
                for ($d = 0; $d < 7; $d++) {
                    $dayDate=$weekStart->copy()->addDays($d);
                    if ($eventDate->isSameDay($dayDate)) {
                    $eventsByDay[$d][] = $evento;
                    break;
                    }
                    }
                    }
                    @endphp

                    <div class="space-y-3">
                        @for ($dayIndex = 0; $dayIndex < 7; $dayIndex++)
                            @php
                            $day=$weekStart->copy()->addDays($dayIndex);
                            $isToday = $day->isToday();
                            $hasEvents = count($eventsByDay[$dayIndex]) > 0;

                            // LÓGICA DE FORMATO DE FECHA:
                            // Si es español: "18 de Diciembre"
                            // Si es inglés: "December 18"
                            $dateFormat = app()->getLocale() === 'es' ? 'D [de] MMMM' : 'MMMM D';
                            @endphp

                            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                {{-- Header del día --}}
                                <div class="{{ $isToday ? 'bg-primary-50 dark:bg-primary-900/20 border-b border-primary-200 dark:border-primary-800' : 'bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700' }}"
                                    style="padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="{{ $isToday ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
                                        style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                                        {{ $day->format('d') }}
                                    </div>
                                    <div>
                                        <div class="{{ $isToday ? 'text-primary-700 dark:text-primary-400' : 'text-gray-900 dark:text-white' }}" style="font-weight: 600; text-transform: capitalize;">
                                            {{-- TRADUCCIÓN DÍA --}}
                                            {{ $day->locale(app()->getLocale())->isoFormat('dddd') }}
                                        </div>
                                        <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.75rem;">
                                            {{-- TRADUCCIÓN FECHA COMPLETA --}}
                                            {{ $day->locale(app()->getLocale())->isoFormat($dateFormat) }}
                                        </div>
                                    </div>
                                    @if($isToday)
                                    <span class="bg-primary-600 text-white" style="margin-left: auto; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.625rem; font-weight: 700; text-transform: uppercase;">
                                        {{ __('Today') }}
                                    </span>
                                    @endif
                                </div>

                                {{-- Eventos del día --}}
                                @if($hasEvents)
                                <div style="padding: 0.5rem;">
                                    <div class="space-y-2">
                                        @foreach ($eventsByDay[$dayIndex] as $evento)
                                        @php
                                        $start = \Carbon\Carbon::parse($evento['start']);
                                        $end = \Carbon\Carbon::parse($evento['end']);

                                        $styles = match ($evento['type']) {
                                        'exam' => 'background-color: #FEF2F2; border-left: 4px solid #EF4444; color: #991B1B;',
                                        default => match ($evento['estado']) {
                                        'en_curso' => 'background-color: #DCFCE7; border-left: 4px solid #22C55E; color: #14532D;',
                                        'completado' => 'background-color: #F3F4F6; border-left: 4px solid #9CA3AF; color: #4B5563;',
                                        default => 'background-color: #E0F2FE; border-left: 4px solid #0EA5E9; color: #0C4A6E;',
                                        }
                                        };

                                        $iconName = $evento['type'] === 'exam' ? 'heroicon-s-clipboard-document-check' : 'heroicon-m-clock';
                                        $alertIcon = $evento['type'] === 'exam' ? 'heroicon-s-exclamation-circle' : null;
                                        @endphp

                                        <div wire:click="openDetailModal({{ $evento['id'] }}, '{{ $evento['type'] }}')"
                                            class="hover:shadow-md active:scale-[0.98]"
                                            style="{{ $styles }}; cursor: pointer; border-radius: 0 0.5rem 0.5rem 0; padding: 0.75rem 1rem; transition: all 0.2s;">

                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                                <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0; flex: 1;">
                                                    @if($alertIcon)
                                                    <div style="width: 18px; height: 18px; flex-shrink: 0; color: #DC2626;">
                                                        <x-icon name="{{ $alertIcon }}" style="width: 100%; height: 100%;" />
                                                    </div>
                                                    @endif
                                                    <span style="font-weight: 700; font-size: 0.875rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0;">
                                                        {{ $evento['title'] }}
                                                    </span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 0.25rem; flex-shrink: 0; opacity: 0.8;">
                                                    <div style="width: 14px; height: 14px; min-width: 12px; flex-shrink: 0;">
                                                        <x-icon name="{{ $iconName }}" style="width: 100%; height: 100%;" />
                                                    </div>
                                                    <span style="font-size: 0.75rem; font-weight: 500;">
                                                        {{ $start->format('H:i') }} - {{ $end->format('H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @else
                                <div style="padding: 0.75rem 1rem;">
                                    <span class="text-gray-400 dark:text-gray-500" style="font-size: 0.8rem; font-style: italic;">
                                        {{-- Podés agregar 'No classes' a tu es.json/en.json --}}
                                        {{ __('No classes') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            @endfor
                    </div>
            </div>

            {{-- ================================================================================= --}}
            {{-- VISTA DESKTOP (GRID) --}}
            {{-- ================================================================================= --}}
            <div class="calendar-desktop-view bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                {{-- Header Días --}}
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); border-top: 1px solid rgba(128,128,128,0.1); border-left: 1px solid rgba(128,128,128,0.1); border-bottom: 1px solid rgba(128,128,128,0.2);">
                    @for ($i = 0; $i < 7; $i++)
                        @php
                        $day=$this->getWeekStart()->copy()->addDays($i);
                        $isToday = $day->isToday();
                        $bgColor = $isToday ? 'rgba(var(--primary-500), 0.1)' : 'transparent';
                        @endphp
                        <div style="padding: 1rem 0.5rem; text-align: center; border-right: 1px solid rgba(128,128,128,0.1); background-color: {{ $bgColor }};">
                            <div class="text-gray-400" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                                {{-- TRADUCCIÓN DÍA CORTO (LUN, MAR / MON, TUE) --}}
                                {{ $day->locale(app()->getLocale())->isoFormat('ddd') }}
                            </div>
                            <div style="display: flex; justify-content: center;">
                                <span class="{{ $isToday ? 'bg-primary-600 text-white' : 'text-gray-900 dark:text-gray-100' }}"
                                    style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; font-size: 0.875rem; font-weight: 700; border-radius: 9999px;">
                                    {{ $day->format('d') }}
                                </span>
                            </div>
                        </div>
                        @endfor
                </div>

                {{-- Body Eventos --}}
                <div style="position: relative; min-height: 400px; background-color: rgba(128,128,128,0.02);">
                    {{-- Líneas verticales --}}
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); position: absolute; inset: 0; pointer-events: none;">
                        @for ($i = 0; $i < 7; $i++)
                            <div style="{{ $i === 0 ? 'border-left: 1px solid rgba(128,128,128,0.1);' : '' }} border-right: 1px solid rgba(128,128,128,0.1); border-bottom: 1px solid rgba(128,128,128,0.2); height: 100%;">
                    </div>
                    @endfor
                </div>

                @php
                $weekStart = $this->getWeekStart()->startOfDay();
                $eventsByDay = array_fill(0, 7, []);

                foreach ($this->getEventos() as $evento) {
                $eventDate = \Carbon\Carbon::parse($evento['start'])->startOfDay();
                for ($d = 0; $d < 7; $d++) {
                    $dayDate=$weekStart->copy()->addDays($d);
                    if ($eventDate->isSameDay($dayDate)) {
                    $eventsByDay[$d][] = $evento;
                    break;
                    }
                    }
                    }
                    @endphp

                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); position: relative; z-index: 10;">
                        @for ($dayIndex = 0; $dayIndex < 7; $dayIndex++)
                            <div style="display: flex; flex-direction: column; gap: 4px; padding: 4px; min-height: 100px; overflow: hidden; min-width: 0;">
                            @foreach ($eventsByDay[$dayIndex] as $evento)
                            @php
                            $start = \Carbon\Carbon::parse($evento['start']);
                            $end = \Carbon\Carbon::parse($evento['end']);

                            $styles = match ($evento['type']) {
                            'exam' => 'background-color: #FEF2F2; border-left: 4px solid #EF4444; color: #991B1B; box-shadow: 0 0 0 1px #FECACA;',
                            default => match ($evento['estado']) {
                            'en_curso' => 'background-color: #DCFCE7; border-left: 4px solid #22C55E; color: #14532D;',
                            'completado' => 'background-color: #F3F4F6; border-left: 4px solid #9CA3AF; color: #4B5563;',
                            default => 'background-color: #E0F2FE; border-left: 4px solid #0EA5E9; color: #0C4A6E;',
                            }
                            };

                            $iconName = $evento['type'] === 'exam' ? 'heroicon-s-clipboard-document-check' : 'heroicon-m-clock';
                            $alertIcon = $evento['type'] === 'exam' ? 'heroicon-s-exclamation-circle' : null;
                            @endphp

                            <div wire:click="openDetailModal({{ $evento['id'] }}, '{{ $evento['type'] }}')"
                                class="hover:shadow-md"
                                style="{{ $styles }}; cursor: pointer; border-radius: 0 0.375rem 0.375rem 0; padding: 0.5rem; font-size: 0.75rem; display: flex; flex-direction: column; justify-content: center; overflow: hidden; transition: all 0.2s; min-width: 0;">

                                <div style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: flex; align-items: center; gap: 0.25rem; min-width: 0;">
                                    @if($alertIcon)
                                    <div style="width: 14px; height: 14px; min-width: 14px; flex-shrink: 0; color: #DC2626;">
                                        <x-icon name="{{ $alertIcon }}" style="width: 100%; height: 100%;" />
                                    </div>
                                    @endif
                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0;">
                                        {{ $evento['title'] }}
                                    </span>
                                </div>

                                <div style="display: flex; align-items: center; gap: 0.25rem; opacity: 0.9; margin-top: 0.125rem; min-width: 0;">
                                    <div style="width: 12px; height: 12px; min-width: 12px; flex-shrink: 0;">
                                        <x-icon name="{{ $iconName }}" style="width: 100%; height: 100%;" />
                                    </div>
                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $start->format('H:i') }} - {{ $end->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                    </div>
                    @endfor
            </div>
        </div>
        </div>
        </div>
    </x-filament::section>

    {{-- MODAL (Sin cambios) --}}
    <x-filament::modal id="detail-modal" width="md" alignment="center">
        @if($modalRecord)
        <x-slot name="heading">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="{{ $modalType === 'exam' ? 'bg-red-100 dark:bg-red-900' : 'bg-primary-100 dark:bg-primary-900' }}"
                    style="padding: 0.5rem; border-radius: 9999px;">
                    <div style="width: 20px; height: 20px;">
                        @if($modalType === 'exam')
                        <x-heroicon-o-clipboard-document-check style="width: 100%; height: 100%; color: var(--danger-600);" />
                        @else
                        <x-heroicon-o-book-open style="width: 100%; height: 100%; color: var(--primary-600);" />
                        @endif
                    </div>
                </div>
                <span>{{ $modalRecord->course->name }}</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            @if($modalType === 'exam')
            <div class="text-center p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <h2 class="text-lg font-bold text-red-700 dark:text-red-400 uppercase">{{ $modalRecord->title }}</h2>
            </div>
            @endif

            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700"
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{-- También traducimos la fecha en el modal --}}
                        {{ $modalType === 'exam' 
                                ? $modalRecord->date->format('d/m/Y') 
                                : $modalRecord->start_time->format('d/m/Y') 
                            }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">{{ __('Time') }}</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        @if($modalType === 'class')
                        {{ $modalRecord->start_time->format('H:i') }} - {{ $modalRecord->end_time?->format('H:i') }}
                        @else
                        <span>{{ __('Class Time') }}</span>
                        @endif
                    </p>
                </div>
            </div>

            @php
            $description = $modalType === 'exam' ? $modalRecord->description : $modalRecord->topic;
            $label = $modalType === 'exam' ? __('Topics to evaluate') : __('Today\'s Topic');
            @endphp

            @if($description)
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">{{ $label }}</p>
                <div class="text-sm bg-white dark:bg-gray-900 p-3 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                    {{ $description }}
                </div>
            </div>
            @endif

            <div style="display: flex; justify-content: flex-end; padding-top: 0.5rem;">
                <x-filament::button color="gray" wire:click="$dispatch('close-modal', { id: 'detail-modal' })">
                    {{ __('Close') }}
                </x-filament::button>
            </div>
        </div>
        @endif
    </x-filament::modal>
</x-filament::widget>