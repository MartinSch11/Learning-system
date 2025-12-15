<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->nullable() // CLAVE: Permite alumnos sin usuario (niños)
                  ->unique()   // Si tiene usuario, es único para ese alumno
                  ->constrained()
                  ->nullOnDelete(); // Si borrás el usuario, el alumno queda pero desvinculado
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};