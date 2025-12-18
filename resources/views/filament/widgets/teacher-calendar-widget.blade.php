<x-filament::widget>
    <x-filament::section>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            
            {{-- TOOLBAR SUPERIOR --}}
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                
                {{-- Controles de navegación --}}
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700" 
                     style="display: flex; align-items: center; padding: 0.25rem;">
                    
                    <button wire:click="$set('weekOffset', {{ $weekOffset - 1 }})"
                        class="hover:bg-white dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400"
                        style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: transparent; cursor: pointer;">
                        {{-- Icono Izquierda con tamaño forzado --}}
                        <div style="width: 20px; height: 20px;">
                            <x-heroicon-m-chevron-left style="width: 100%; height: 100%;" />
                        </div>
                    </button>

                    <button type="button" wire:click="$set('weekOffset', 0)"
                        class="{{ $weekOffset === 0 ? 'bg-white dark:bg-gray-600 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-600 dark:text-gray-300' }}"
                        style="padding: 0.375rem 1rem; margin: 0 0.25rem; font-size: 0.875rem; font-weight: 600; border-radius: 0.375rem; border: none; cursor: pointer;">
                        Hoy
                    </button>

                    <button wire:click="$set('weekOffset', {{ $weekOffset + 1 }})"
                        class="hover:bg-white dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400"
                        style="padding: 0.5rem; border-radius: 0.375rem; border: none; background: transparent; cursor: pointer;">
                        {{-- Icono Derecha con tamaño forzado --}}
                        <div style="width: 20px; height: 20px;">
                            <x-heroicon-m-chevron-right style="width: 100%; height: 100%;" />
                        </div>
                    </button>
                </div>

                {{-- Título del Mes --}}
                <div class="text-gray-900 dark:text-white" 
                     style="font-size: 1.25rem; font-weight: 700; text-transform: capitalize; display: flex; align-items: center; gap: 0.5rem;">
                    {{-- Icono Calendario con tamaño forzado --}}
                    <div style="width: 24px; height: 24px; color: var(--primary-500);">
                        <x-heroicon-o-calendar-days style="width: 100%; height: 100%;" />
                    </div>
                    <span>{{ $this->getWeekStart()->locale('es')->isoFormat('MMMM YYYY') }}</span>
                </div>
            </div>

            {{-- CONTENEDOR DEL CALENDARIO --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                {{-- Scroll horizontal forzado --}}
                <div style="overflow-x: auto;">
                    <div style="min-width: 800px;">
                        
                        {{-- Cabecera de Días --}}
                        <div style="display: grid; grid-template-columns: repeat(7, 1fr);border-top: 1px solid rgba(128,128,128,0.1); border-left: 1px solid rgba(128,128,128,0.1); border-bottom: 1px solid rgba(128,128,128,0.2);">
                            @for ($i = 0; $i < 7; $i++)
                                @php
                                    $day = $this->getWeekStart()->copy()->addDays($i);
                                    $isToday = $day->isToday();
                                    $bgColor = $isToday ? 'rgba(var(--primary-500), 0.1)' : 'transparent';
                                @endphp
                                <div style="padding: 1rem 0.5rem; text-align: center; border-right: 1px solid rgba(128,128,128,0.1); background-color: {{ $bgColor }};">
                                    <div class="text-gray-400" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                                        {{ $day->locale('es')->isoFormat('ddd') }}
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

                        {{-- Cuerpo del Calendario --}}
                        <div style="position: relative; min-height: 400px; background-color: rgba(128,128,128,0.02);">
                            
                            {{-- Fondo de columnas --}}
                            <div style="display: grid; grid-template-columns: repeat(7, 1fr); position: absolute; inset: 0; pointer-events: none;">
                                @for ($i = 0; $i < 7; $i++)
                                    <div style="border-left: 1px solid rgba(128,128,128,0.1);border-right: 1px solid rgba(128,128,128,0.1); height: 100%;"></div>
                                @endfor
                            </div>

                            {{-- Capa de Eventos --}}
                            <div style="display: grid; grid-template-columns: repeat(7, 1fr); grid-auto-rows: 50px; gap: 4px; padding: 4px; position: relative; z-index: 10;">
                                @php
                                    $weekStart = $this->getWeekStart();
                                    $occupied = array_fill(0, 7, []);
                                @endphp

                                @foreach ($this->getEventos() as $evento)
                                    @php
                                        $start = \Carbon\Carbon::parse($evento['start']);
                                        $end = \Carbon\Carbon::parse($evento['end']);
                                        
                                        $startDay = $weekStart->diffInDays($start->copy()->startOfDay(), false);
                                        $endDay = $weekStart->diffInDays($end->copy()->startOfDay(), false);
                                        
                                        $startDay = max(0, min(6, $startDay));
                                        $endDay = max(0, min(6, $endDay));
                                        $span = max(1, $endDay - $startDay + 1);

                                        // Algoritmo de filas
                                        $row = 1;
                                        while (true) {
                                            $conflict = false;
                                            for ($d = $startDay; $d <= $endDay; $d++) {
                                                if (in_array($row, $occupied[$d])) { $conflict = true; break; }
                                            }
                                            if (!$conflict) break;
                                            $row++;
                                        }
                                        for ($d = $startDay; $d <= $endDay; $d++) $occupied[$d][] = $row;

                                        // Estilos inline para colores
                                        $styleColor = match ($evento['estado']) {
                                            'pendiente' => 'background-color: #E0F2FE; border-left: 4px solid #0EA5E9; color: #0C4A6E;',
                                            'en_curso' => 'background-color: #DCFCE7; border-left: 4px solid #22C55E; color: #14532D;',
                                            'completado' => 'background-color: #F3F4F6; border-left: 4px solid #9CA3AF; color: #4B5563; opacity: 0.8;',
                                            default => 'background-color: #ffffff; border-left: 4px solid #ccc;',
                                        };
                                    @endphp

                                    <div style="grid-column: {{ $startDay + 1 }} / span {{ $span }}; grid-row: {{ $row }};">
                                        <div wire:click="openReservaModal({{ $evento['id'] }})"
                                             class="hover:shadow-md"
                                             style="{{ $styleColor }}; cursor: pointer; width: 100%; height: 100%; border-radius: 0 0.375rem 0.375rem 0; padding: 0.5rem; font-size: 0.75rem; display: flex; flex-direction: column; justify-content: center; overflow: hidden; transition: all 0.2s;">
                                            
                                            <div style="font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $evento['title'] }}
                                            </div>
                                            
                                            <div style="display: flex; align-items: center; gap: 0.25rem; opacity: 0.9; margin-top: 0.125rem;">
                                                {{-- Reloj con tamaño fijo --}}
                                                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $start->format('H:i') }} - {{ $end->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- MODAL (ESTE SUELE ANDAR BIEN PORQUE USA COMPONENTES DE FILAMENT) --}}
    <x-filament::modal id="reserva-modal" width="md" alignment="center">
        @if($record)
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div class="bg-primary-100 dark:bg-primary-900" style="padding: 0.5rem; border-radius: 9999px;">
                        <div style="width: 20px; height: 20px;">
                            <x-heroicon-o-academic-cap style="width: 100%; height: 100%; color: var(--primary-600);" />
                        </div>
                    </div>
                    <span>{{ $record->course->name }}</span>
                </div>
            </x-slot>

            <div class="space-y-6">
                <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Fecha</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $record->start_time->locale('es')->isoFormat('ddd D MMM') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Horario</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $record->start_time->format('H:i') }} - {{ $record->end_time?->format('H:i') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($record->topic)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Tema del día</p>
                        <div class="text-sm bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg border border-amber-200 dark:border-amber-800 text-gray-700 dark:text-gray-300">
                            {{ $record->topic }}
                        </div>
                    </div>
                @endif

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem;">
                    <x-filament::button color="gray" wire:click="$dispatch('close-modal', { id: 'reserva-modal' })">
                        Cerrar
                    </x-filament::button>
                    <x-filament::button wire:click="irATomarAsistencia" icon="heroicon-m-clipboard-document-check">
                        Tomar Asistencia
                    </x-filament::button>
                </div>
            </div>
        @endif
    </x-filament::modal>
</x-filament::widget>