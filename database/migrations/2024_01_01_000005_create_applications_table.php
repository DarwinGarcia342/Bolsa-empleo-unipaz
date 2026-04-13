<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');        // Estudiante
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade'); // Vacante
            $table->text('cover_letter')->nullable();           // Carta de presentación
            $table->string('cv_path')->nullable();              // CV adjunto en esta postulación
            $table->enum('status', ['pending', 'reviewed', 'interview', 'accepted', 'rejected'])
                  ->default('pending');
            $table->text('company_notes')->nullable();          // Notas internas de la empresa
            $table->timestamp('reviewed_at')->nullable();
            $table->unique(['user_id', 'job_posting_id']);      // Un estudiante no puede postularse dos veces
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
