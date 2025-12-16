<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Agregamos el nombre del padre/tutor
            $table->string('parent_name')->nullable()->after('phone');

            // Aseguramos que esté la relación con el usuario (si ya la tenías, borrá esta línea)
            // Si ya corriste la migración 'add_user_id...', no pongas esto de nuevo.
            if (!Schema::hasColumn('students', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('parent_name');
            // $table->dropForeign(['user_id']); // Descomentar si borrás la columna
            // $table->dropColumn('user_id');
        });
    }
};
