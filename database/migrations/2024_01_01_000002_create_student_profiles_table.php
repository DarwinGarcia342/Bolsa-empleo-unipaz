<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_code')->nullable();  // Código estudiantil
            $table->string('program');                   // Programa académico
            $table->string('semester')->nullable();
            $table->string('phone')->nullable();
            $table->text('about')->nullable();           // Descripción personal
            $table->string('cv_path')->nullable();       // Ruta al PDF de hoja de vida
            $table->string('linkedin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
