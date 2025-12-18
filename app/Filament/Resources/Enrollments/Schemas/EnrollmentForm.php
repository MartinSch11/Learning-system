<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker; // Necesario para el formulario de alumno
use Filament\Forms\Components\Toggle;     // Necesario para el formulario de alumno
use Filament\Schemas\Components\Section;    // Necesario para el formulario de alumno
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label(__('Student'))
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    // === AGREGADO: Formulario de Creación Rápida ===
                    ->createOptionForm([
                        Section::make(__('Student Information'))
                            ->icon('heroicon-m-academic-cap')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Full Name'))
                                    ->required(),

                                TextInput::make('dni')
                                    ->label(__('National ID'))
                                    ->required()
                                    ->numeric(),

                                DatePicker::make('birth_date')
                                    ->label(__('Birth Date'))
                                    ->required(),

                                Toggle::make('active')
                                    ->label(__('Active'))
                                    ->default(true)
                                    ->required()
                                    ->onColor('success'),
                            ])
                            ->collapsible(),

                        // SECCIÓN 2
                        Section::make(__('Emergency Contact'))
                            ->icon('heroicon-m-phone')
                            ->columns(2)
                            ->schema([
                                TextInput::make('parent_name')
                                    ->label(__('Parent/Guardian Name'))
                                    ->nullable(),

                                TextInput::make('phone')
                                    ->label(__('Phone Number'))
                                    ->tel()
                                    ->nullable(),
                            ])
                            ->collapsed(),


                        Section::make(__('System Access Data'))
                            ->description(__('These credentials will be used for login.'))
                            ->icon('heroicon-m-key')
                            ->columns(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email()
                                    ->required(),

                                TextInput::make('password')
                                    ->label(__('Password'))
                                    ->password()
                                    ->nullable() // <--- IMPORTANTE: Permitir null en el form
                                    ->helperText(__('If left empty, password will be the National ID.')),
                            ])
                            ->collapsed(),
                    ])

                    // === LÓGICA DE GUARDADO BLINDADA Y DEBUGGEABLE ===
                    ->createOptionUsing(function (array $data) {
                        try {
                            // 1. Validar datos mínimos
                            if (empty($data['email']) || empty($data['name']) || empty($data['dni'])) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Faltan datos obligatorios')
                                    ->danger()
                                    ->send();
                                return null;
                            }

                            // 2. Definir contraseña
                            $password = !empty($data['password']) ? $data['password'] : $data['dni'];

                            // 3. Crear Usuario (Usamos transacción por seguridad)
                            return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $password) {
                                $user = User::create([
                                    'name'     => $data['name'],
                                    'email'    => $data['email'],
                                    'password' => Hash::make($password),
                                ]);

                                $user->assignRole('student');

                                // 4. Crear Alumno
                                $student = Student::create([
                                    'user_id'       => $user->id,
                                    'name'          => $data['name'],
                                    'dni'           => $data['dni'],
                                    'birth_date'    => $data['birth_date'],
                                    'parent_name'   => $data['parent_name'],
                                    'phone'         => $data['phone'],
                                    'active'        => true,
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Alumno creado correctamente')
                                    ->success()
                                    ->send();

                                return $student->id;
                            });
                        } catch (\Exception $e) {
                            // Si algo falla (ej: email duplicado que pasó el filtro), avisamos
                            \Filament\Notifications\Notification::make()
                                ->title('Error al crear alumno')
                                ->body($e->getMessage()) // Esto te va a decir QUÉ pasó
                                ->danger()
                                ->send();

                            return null;
                        }
                    }),

                Select::make('course_id')
                    ->label(__('Course'))
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('year')
                    ->label(__('Academic Year'))
                    ->numeric()
                    ->default(date('Y'))
                    ->required(),

                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'cursando' => __('Studying'),
                        'aprobado' => __('Approved'),
                        'inscripto' => __('Enrolled'),
                    ])
                    ->default('inscripto')
                    ->required(),
            ]);
    }
}
