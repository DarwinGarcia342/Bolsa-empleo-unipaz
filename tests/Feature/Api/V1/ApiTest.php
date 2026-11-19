<?php

namespace Tests\Feature\Api\V1;

use App\Models\Application;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\LawReference;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Tests\TestCase;

class ApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('role')->default('student');
            $table->string('google_id')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('student_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('student_code')->nullable();
            $table->string('program');
            $table->string('semester')->nullable();
            $table->string('phone')->nullable();
            $table->text('about')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('linkedin')->nullable();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('company_name');
            $table->string('nit')->nullable();
            $table->string('sector')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('job_postings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id');
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->text('responsibilities');
            $table->string('area')->nullable();
            $table->string('contract_type');
            $table->string('modality');
            $table->string('schedule')->nullable();
            $table->string('location');
            $table->string('salary_range')->nullable();
            $table->boolean('salary_negotiable')->default(false);
            $table->integer('positions')->default(1);
            $table->date('deadline');
            $table->string('status')->default('active');
            $table->boolean('requires_cv')->default(true);
            $table->text('programs_targeted')->nullable();
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('job_posting_id');
            $table->text('cover_letter')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('status')->default('pending');
            $table->text('company_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_favorite_job_postings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('job_posting_id');
            $table->timestamps();
            $table->unique(['user_id', 'job_posting_id']);
        });

        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('law_references', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('applicable_to')->nullable();
            $table->string('category');
            $table->string('law_number')->nullable();
            $table->date('publication_date')->nullable();
            $table->text('relevant_articles')->nullable();
            $table->text('implementation_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function test_student_can_login_use_and_revoke_a_device_token(): void
    {
        $user = User::factory()->create([
            'email' => 'student@unipaz.edu.co',
            'password' => 'correct-password',
            'role' => 'student',
            'active' => true,
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'correct-password',
            'device_name' => 'android',
        ]);

        $login->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.role', 'student');

        $token = $login->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
        Auth::forgetGuards();

        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertUnauthorized();
    }

    public function test_password_login_rejects_noninstitutional_or_unverified_students(): void
    {
        $externalStudent = User::factory()->create([
            'email' => 'student@example.com',
            'role' => 'student',
        ]);
        $unverifiedStudent = User::factory()->unverified()->create([
            'email' => 'unverified@unipaz.edu.co',
            'role' => 'student',
        ]);

        foreach ([$externalStudent, $unverifiedStudent] as $student) {
            $this->postJson('/api/v1/auth/login', [
                'email' => $student->email,
                'password' => 'password',
                'device_name' => 'android',
            ])->assertForbidden();
        }

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_student_can_authenticate_with_a_verified_unipaz_google_token(): void
    {
        $email = 'google.student@unipaz.edu.co';
        $googleUser = (new GoogleUser)->setRaw([
            'sub' => 'google-student-id',
            'email' => $email,
            'email_verified' => true,
        ])->map([
            'id' => 'google-student-id',
            'email' => $email,
            'name' => 'Google Student',
        ]);
        $provider = \Mockery::mock();
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('userFromToken')->with('google-id-token')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $response = $this->postJson('/api/v1/auth/google', [
            'id_token' => 'google-id-token',
            'device_name' => 'android',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.email', $email)
            ->assertJsonPath('user.role', 'student');

        $this->assertDatabaseHas('student_profiles', [
            'user_id' => $response->json('user.id'),
            'program' => 'Sin especificar',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_inactive_accounts_cannot_receive_api_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'correct-password',
            'active' => false,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'correct-password',
            'device_name' => 'android',
        ])->assertForbidden();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_existing_tokens_cannot_access_api_after_account_is_deactivated(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'active' => true,
        ]);
        $token = $this->loginAs($user);

        $user->update(['active' => false]);
        Auth::forgetGuards();

        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertForbidden();

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();
    }

    public function test_only_students_can_use_student_api_routes(): void
    {
        $user = User::factory()->create([
            'password' => 'correct-password',
            'role' => 'company',
            'active' => true,
        ]);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'correct-password',
            'device_name' => 'ios',
        ])->assertOk()->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/student/favorites')
            ->assertForbidden();
    }

    public function test_student_can_search_vacancies_and_manage_favorites(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $companyUser = User::factory()->create(['role' => 'company']);
        $company = Company::create([
            'user_id' => $companyUser->id,
            'company_name' => 'UNIPAZ Labs',
            'status' => 'approved',
        ]);
        $job = $this->createJob($company);
        $token = $this->loginAs($student);

        $this->getJson('/api/v1/jobs?area=engineering')
            ->assertOk()
            ->assertJsonPath('data.0.id', $job->id)
            ->assertJsonPath('data.0.company.name', 'UNIPAZ Labs');

        $this->withToken($token)
            ->putJson("/api/v1/jobs/{$job->id}/favorite")
            ->assertOk()
            ->assertJsonPath('data.is_favorite', true);

        $this->withToken($token)
            ->getJson('/api/v1/student/favorites')
            ->assertOk()
            ->assertJsonPath('data.0.id', $job->id);

        $this->withToken($token)
            ->deleteJson("/api/v1/jobs/{$job->id}/favorite")
            ->assertOk()
            ->assertJsonPath('data.is_favorite', false);
    }

    public function test_student_can_filter_vacancies_by_location(): void
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        $company = Company::create([
            'user_id' => $companyUser->id,
            'company_name' => 'UNIPAZ Labs',
            'status' => 'approved',
        ]);
        $matchingJob = $this->createJob($company, ['location' => 'Barrancabermeja']);
        $this->createJob($company, ['title' => 'Vacante en Bogotá', 'location' => 'Bogotá']);

        $this->getJson('/api/v1/jobs?location=Barrancabermeja')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingJob->id);
    }

    public function test_student_can_apply_once_to_an_active_vacancy(): void
    {
        Notification::fake();

        $student = User::factory()->create(['role' => 'student']);
        $companyUser = User::factory()->create(['role' => 'company']);
        $company = Company::create([
            'user_id' => $companyUser->id,
            'company_name' => 'UNIPAZ Labs',
            'status' => 'approved',
        ]);
        $job = $this->createJob($company, ['requires_cv' => false]);
        $token = $this->loginAs($student);

        $this->withToken($token)
            ->postJson("/api/v1/jobs/{$job->id}/applications", [
                'cover_letter' => 'Me interesa esta oportunidad.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.job.id', $job->id);

        $this->withToken($token)
            ->postJson("/api/v1/jobs/{$job->id}/applications")
            ->assertStatus(409);

        $this->withToken($token)
            ->getJson('/api/v1/student/applications')
            ->assertOk()
            ->assertJsonPath('data.0.job.id', $job->id);
    }

    public function test_company_can_only_manage_its_own_jobs_and_needs_approval_to_publish(): void
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        $company = Company::create([
            'user_id' => $companyUser->id,
            'company_name' => 'Empresa A',
            'status' => 'approved',
        ]);
        $otherCompanyUser = User::factory()->create(['role' => 'company']);
        $otherCompany = Company::create([
            'user_id' => $otherCompanyUser->id,
            'company_name' => 'Empresa B',
            'status' => 'approved',
        ]);
        $ownJob = $this->createJob($company);
        $otherJob = $this->createJob($otherCompany, ['title' => 'Vacante privada']);
        $token = $this->loginAs($companyUser);

        $this->withToken($token)
            ->getJson('/api/v1/company/jobs')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownJob->id);

        $this->withToken($token)
            ->putJson("/api/v1/company/jobs/{$otherJob->id}", [])
            ->assertNotFound();

        $pendingUser = User::factory()->create(['role' => 'company']);
        Company::create([
            'user_id' => $pendingUser->id,
            'company_name' => 'Pendiente',
            'status' => 'pending',
        ]);
        $pendingToken = $this->loginAs($pendingUser);

        $this->withToken($pendingToken)
            ->postJson('/api/v1/company/jobs', [])
            ->assertForbidden();
    }

    public function test_company_can_publish_a_job_with_the_mobile_schedule_field(): void
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        Company::create([
            'user_id' => $companyUser->id,
            'company_name' => 'Empresa aprobada',
            'status' => 'approved',
        ]);
        $token = $this->loginAs($companyUser);

        $response = $this->withToken($token)
            ->postJson('/api/v1/company/jobs', [
                'title' => 'Desarrollador móvil',
                'description' => 'Desarrollar aplicaciones para dispositivos móviles.',
                'requirements' => 'Experiencia en React Native y TypeScript.',
                'responsibilities' => 'Construir y mantener aplicaciones móviles.',
                'area' => 'Tecnología',
                'contract_type' => 'Prácticas',
                'modality' => 'hybrid',
                'schedule' => 'Lunes a viernes',
                'location' => 'Barrancabermeja',
                'positions' => 1,
                'deadline' => now()->addDays(5)->toDateString(),
                'programs_targeted' => 'Ingeniería Informática',
            ])
            ->assertCreated()
            ->assertJsonPath('data.schedule', 'Lunes a viernes')
            ->assertJsonPath('data.programs_targeted', 'Ingeniería Informática');

        $this->getJson('/api/v1/jobs/'.$response->json('data.id'))
            ->assertOk()
            ->assertJsonPath('data.schedule', 'Lunes a viernes')
            ->assertJsonPath('data.programs_targeted', 'Ingeniería Informática');
    }

    public function test_admin_can_approve_companies_and_company_accounts_cannot_use_admin_api(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $companyUser = User::factory()->create(['role' => 'company']);
        $company = Company::create([
            'user_id' => $companyUser->id,
            'company_name' => 'Pendiente',
            'status' => 'pending',
        ]);
        $adminToken = $this->loginAs($admin);

        $this->withToken($adminToken)
            ->getJson('/api/v1/admin/companies?status=pending')
            ->assertOk()
            ->assertJsonPath('data.0.id', $company->id);

        $this->withToken($adminToken)
            ->postJson("/api/v1/admin/companies/{$company->id}/approve")
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');

        $companyToken = $this->loginAs($companyUser);
        $this->withToken($companyToken)
            ->getJson('/api/v1/admin/dashboard')
            ->assertForbidden();

        Auth::forgetGuards();
        $this->withToken($adminToken)
            ->patchJson("/api/v1/admin/users/{$companyUser->id}/toggle")
            ->assertOk()
            ->assertJsonPath('data.active', false);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $companyUser->id,
        ]);
    }

    public function test_companies_can_register_through_the_api_pending_administrator_approval(): void
    {
        $this->postJson('/api/v1/auth/register/company', [
            'name' => 'Contacto',
            'email' => 'registro@empresa.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Empresa nueva',
            'sector' => 'Tecnología',
            'contact_person' => 'Contacto',
            'phone' => '3001234567',
            'address' => 'Barrancabermeja',
            'description' => 'Empresa de desarrollo de software.',
        ])->assertCreated()
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('users', [
            'email' => 'registro@empresa.test',
            'role' => 'company',
        ]);
    }

    public function test_companies_can_register_with_the_mobile_apps_minimal_payload(): void
    {
        $this->postJson('/api/v1/auth/register/company', [
            'name' => 'Contacto',
            'email' => 'mobile-registration@empresa.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Empresa móvil',
            'nit' => '900123456',
            'contact_person' => 'Contacto',
        ])->assertCreated()
            ->assertJsonPath('data.status', 'pending');
    }

    public function test_student_can_upload_a_cv_to_the_profile_cv_endpoint(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'student']);
        $profile = $student->studentProfile()->create(['program' => 'Ingeniería Informática']);
        $token = $this->loginAs($student);

        $this->withToken($token)
            ->post('/api/v1/student/profile/cv', [
                'cv' => UploadedFile::fake()->create('hoja-de-vida.pdf', 100, 'application/pdf'),
            ])
            ->assertOk()
            ->assertJsonPath('data.program', 'Ingeniería Informática');

        $this->assertNotEmpty($profile->fresh()->cv_path);
        Storage::disk('public')->assertExists($profile->fresh()->cv_path);
    }

    public function test_company_can_update_status_only_for_its_own_applications(): void
    {
        Notification::fake();

        $companyUser = User::factory()->create(['role' => 'company']);
        $company = Company::create([
            'user_id' => $companyUser->id,
            'company_name' => 'Empresa',
            'status' => 'approved',
        ]);
        $student = User::factory()->create(['role' => 'student']);
        $job = $this->createJob($company, ['requires_cv' => false]);
        $application = Application::create([
            'user_id' => $student->id,
            'job_posting_id' => $job->id,
            'status' => 'pending',
        ]);
        $token = $this->loginAs($companyUser);

        $this->withToken($token)
            ->putJson("/api/v1/company/applications/{$application->id}/status", [
                'status' => 'interview',
                'company_notes' => 'Contactar al estudiante.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'interview');
    }

    public function test_notifications_are_private_and_laws_are_filtered_by_role(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $otherUser = User::factory()->create(['role' => 'student']);
        $notificationId = 'notification-test-1';
        DB::table('notifications')->insert([
            'id' => $notificationId,
            'type' => 'test',
            'notifiable_type' => User::class,
            'notifiable_id' => $student->id,
            'data' => json_encode(['message' => 'Notificación privada']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('notifications')->insert([
            'id' => 'notification-test-2',
            'type' => 'test',
            'notifiable_type' => User::class,
            'notifiable_id' => $otherUser->id,
            'data' => json_encode(['message' => 'Otra notificación']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $law = LawReference::create([
            'title' => 'Norma para estudiantes',
            'description' => 'Contenido aplicable al estudiante.',
            'applicable_to' => ['student'],
            'category' => 'Educación',
            'is_active' => true,
        ]);
        LawReference::create([
            'title' => 'Norma empresarial',
            'description' => 'Contenido aplicable a empresas.',
            'applicable_to' => ['company'],
            'category' => 'Laboral',
            'is_active' => true,
        ]);

        $token = $this->loginAs($student);

        $this->withToken($token)
            ->getJson('/api/v1/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('data.unread_count', 1);

        $this->withToken($token)
            ->patchJson("/api/v1/notifications/{$notificationId}/read")
            ->assertOk();

        $this->withToken($token)
            ->patchJson('/api/v1/notifications/notification-test-2/read')
            ->assertNotFound();

        $this->withToken($token)
            ->getJson('/api/v1/laws')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $law->id);
    }

    private function loginAs(User $user): string
    {
        if ($user->isStudent() && ! str_ends_with($user->email, '@unipaz.edu.co')) {
            $user->forceFill([
                'email' => 'student'.$user->id.'@unipaz.edu.co',
                'email_verified_at' => now(),
            ])->save();
        }

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-device',
        ])->assertOk();

        Auth::forgetGuards();

        return $response->json('token');
    }

    private function createJob(Company $company, array $overrides = []): JobPosting
    {
        return JobPosting::create([
            'company_id' => $company->id,
            'title' => 'Desarrollador móvil',
            'description' => 'Desarrollar aplicaciones para dispositivos móviles.',
            'requirements' => 'Experiencia en desarrollo de software.',
            'responsibilities' => 'Construir y mantener aplicaciones.',
            'area' => 'engineering',
            'contract_type' => 'fixed',
            'modality' => 'remote',
            'location' => 'Barrancabermeja',
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => 'active',
            ...$overrides,
        ]);
    }
}
