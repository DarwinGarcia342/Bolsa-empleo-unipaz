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
        Schema::create('law_references', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('applicable_to')->nullable(); // JSON: admin, student, company, all
            $table->string('category'); // Laboral, Datos personales, Educación, Seguridad, etc.
            $table->string('law_number')->nullable(); // Ej: Ley 1581 de 2012
            $table->date('publication_date')->nullable();
            $table->text('relevant_articles')->nullable(); // JSON: artículos principales
            $table->text('implementation_notes')->nullable(); // Notas de implementación
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_references');
    }
};
