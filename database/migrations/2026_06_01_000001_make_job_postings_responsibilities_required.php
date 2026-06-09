<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = config('database.default');
        $defaultResponsibilities = 'Responsabilidades no especificadas';

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            Schema::rename('job_postings', 'job_postings_old');

            DB::statement("UPDATE job_postings_old SET responsibilities = '$defaultResponsibilities' WHERE responsibilities IS NULL OR trim(responsibilities) = ''");

            Schema::create('job_postings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description');
                $table->text('requirements');
                $table->text('responsibilities');
                $table->string('area')->nullable();
                $table->string('contract_type');
                $table->string('modality');
                $table->string('location')->default('Barrancabermeja');
                $table->string('salary_range')->nullable();
                $table->boolean('salary_negotiable')->default(false);
                $table->integer('positions')->default(1);
                $table->date('deadline');
                $table->enum('status', ['active', 'paused', 'closed'])->default('active');
                $table->boolean('requires_cv')->default(true);
                $table->text('programs_targeted')->nullable();
                $table->timestamps();
            });

            DB::statement("INSERT INTO job_postings (id, company_id, title, description, requirements, responsibilities, area, contract_type, modality, location, salary_range, salary_negotiable, positions, deadline, status, requires_cv, programs_targeted, created_at, updated_at)
SELECT id, company_id, title, description, requirements, COALESCE(NULLIF(trim(responsibilities), ''), " . DB::getPdo()->quote($defaultResponsibilities) . "), area, contract_type, modality, location, salary_range, salary_negotiable, positions, deadline, status, requires_cv, programs_targeted, created_at, updated_at FROM job_postings_old");

            $this->rebuildSqliteApplicationForeignKey();
            Schema::drop('job_postings_old');
            DB::statement('PRAGMA foreign_keys = ON');

            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("UPDATE job_postings SET responsibilities = '$defaultResponsibilities' WHERE responsibilities IS NULL OR trim(responsibilities) = ''");
            DB::statement('ALTER TABLE job_postings MODIFY responsibilities TEXT NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("UPDATE job_postings SET responsibilities = '$defaultResponsibilities' WHERE responsibilities IS NULL OR trim(responsibilities) = ''");
            DB::statement('ALTER TABLE job_postings ALTER COLUMN responsibilities SET NOT NULL');
            return;
        }

        throw new \RuntimeException("Unsupported database driver [{$driver}] for making job_postings.responsibilities NOT NULL.");
    }

    public function down(): void
    {
        $driver = config('database.default');

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            Schema::rename('job_postings', 'job_postings_old');

            Schema::create('job_postings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description');
                $table->text('requirements');
                $table->text('responsibilities')->nullable();
                $table->string('area')->nullable();
                $table->string('contract_type');
                $table->string('modality');
                $table->string('location')->default('Barrancabermeja');
                $table->string('salary_range')->nullable();
                $table->boolean('salary_negotiable')->default(false);
                $table->integer('positions')->default(1);
                $table->date('deadline');
                $table->enum('status', ['active', 'paused', 'closed'])->default('active');
                $table->boolean('requires_cv')->default(true);
                $table->text('programs_targeted')->nullable();
                $table->timestamps();
            });

            DB::statement(<<<'SQL'
INSERT INTO job_postings (id, company_id, title, description, requirements, responsibilities, area, contract_type, modality, location, salary_range, salary_negotiable, positions, deadline, status, requires_cv, programs_targeted, created_at, updated_at)
SELECT id, company_id, title, description, requirements, responsibilities, area, contract_type, modality, location, salary_range, salary_negotiable, positions, deadline, status, requires_cv, programs_targeted, created_at, updated_at FROM job_postings_old;
SQL
            );

            $this->rebuildSqliteApplicationForeignKey();
            Schema::drop('job_postings_old');
            DB::statement('PRAGMA foreign_keys = ON');

            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE job_postings MODIFY responsibilities TEXT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE job_postings ALTER COLUMN responsibilities DROP NOT NULL');
            return;
        }

        throw new \RuntimeException("Unsupported database driver [{$driver}] for reverting job_postings.responsibilities nullability.");
    }

    private function rebuildSqliteApplicationForeignKey(): void
    {
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::rename('applications', 'applications_old');

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->string('cv_path')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'interview', 'accepted', 'rejected'])->default('pending');
            $table->text('company_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unique(['user_id', 'job_posting_id'], 'applications_student_job_unique');
            $table->timestamps();
        });

        DB::statement(<<<'SQL'
INSERT INTO applications (id, user_id, job_posting_id, cover_letter, cv_path, status, company_notes, reviewed_at, created_at, updated_at)
SELECT id, user_id, job_posting_id, cover_letter, cv_path, status, company_notes, reviewed_at, created_at, updated_at FROM applications_old;
SQL
        );

        Schema::drop('applications_old');
    }
};
