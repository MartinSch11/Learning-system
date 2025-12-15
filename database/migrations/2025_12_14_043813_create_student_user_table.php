<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_user', function (Blueprint $table) {
            $table->id();
            
            // El Alumno
            $table->foreignId('student_id')
                  ->constrained()
                  ->cascadeOnDelete(); // Si borrás al alumno, se borra el vínculo
            
            // El Familiar (Padre, Madre, Tutor) que tiene el usuario
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Qué relación tienen (padre, madre, tio, tutor)
            $table->string('relationship')->default('parent'); 

            $table->timestamps();

            // Evitar duplicados: No podés ser padre del mismo pibe dos veces
            $table->unique(['student_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_user');
    }
};