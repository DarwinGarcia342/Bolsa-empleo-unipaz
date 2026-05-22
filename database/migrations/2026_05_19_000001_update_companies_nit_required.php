<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Cambiar NIT a bigInteger no nullable (sin permitir duplicados con unique constraint)
            $table->unsignedBigInteger('nit')->change()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('nit')->nullable()->change();
        });
    }
};
