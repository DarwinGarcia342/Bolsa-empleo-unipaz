<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Neutralizado: las tablas users, password_reset_tokens y sessions
    // son creadas por la migración personalizada del proyecto (2024_01_01_000001).
    public function up(): void {}
    public function down(): void {}
};
