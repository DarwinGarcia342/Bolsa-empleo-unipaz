<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');                            // Título del cargo
            $table->text('description');                        // Descripción del puesto
            $table->text('requirements');                       // Requisitos
            $table->text('responsibilities')->nullable();       // Responsabilidades
            $table->string('area')->nullable();                 // Área (sistemas, admin, etc.)
            $table->string('contract_type');                    // Tipo contrato (fijo, indefinido, etc.)
            $table->string('modality');                         // Modalidad (presencial, remoto, híbrido)
            $table->string('location')->default('Barrancabermeja');
            $table->string('salary_range')->nullable();         // Ej: "1.5M - 2M"
            $table->boolean('salary_negotiable')->default(false);
            $table->integer('positions')->default(1);           // Número de vacantes
            $table->date('deadline');                           // Fecha límite de postulación
            $table->enum('status', ['active', 'paused', 'closed'])->default('active');
            $table->boolean('requires_cv')->default(true);
            $table->text('programs_targeted')->nullable();      // Programas académicos preferidos
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
