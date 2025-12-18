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
        Schema::table('exam_grades', function (Blueprint $table) {
            // Cambiamos de decimal a string y le damos espacio suficiente
            $table->string('score', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('exam_grades', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->nullable()->change();
        });
    }
};
