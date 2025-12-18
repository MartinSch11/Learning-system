<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. La Clase del Día (Ej: "Inglés 1 - 14/10/2025")
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users'); // El docente
            $table->dateTime('start_time'); // Fecha y hora
            $table->string('topic')->nullable(); // Tema del día (opcional)
            $table->timestamps();
        });

        // 2. El Presente (Uno por alumno por clase)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            
            // Los Toggles
            $table->boolean('is_present')->default(false); // Arranca ausente
            $table->boolean('is_justified')->default(false); // Justificado
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('class_sessions');
    }
};