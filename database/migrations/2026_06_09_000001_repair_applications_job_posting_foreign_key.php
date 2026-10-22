<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (config('database.default') !== 'sqlite' || !Schema::hasTable('applications')) {
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');

        if (Schema::hasTable('applications_old')) {
            Schema::dropIfExists('applications');
        } else {
            Schema::rename('applications', 'applications_old');
        }

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
SELECT id, user_id, job_posting_id, cover_letter, cv_path, status, company_notes, reviewed_at, created_at, updated_at
FROM applications_old
WHERE EXISTS (SELECT 1 FROM users WHERE users.id = applications_old.user_id)
  AND EXISTS (SELECT 1 FROM job_postings WHERE job_postings.id = applications_old.job_posting_id)
SQL);

        Schema::drop('applications_old');
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        //
    }
};
