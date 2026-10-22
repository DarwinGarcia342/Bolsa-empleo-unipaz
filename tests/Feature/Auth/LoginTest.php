<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class LoginTest extends TestCase
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
            $table->boolean('active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function test_company_can_sign_in_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'company@example.com',
            'password' => 'correct-password',
            'role' => 'company',
            'active' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertRedirect(route('company.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_sign_in_clears_stale_credential_errors(): void
    {
        $user = User::factory()->create([
            'email' => 'company@example.com',
            'password' => 'correct-password',
            'role' => 'company',
            'active' => true,
        ]);
        $errors = new ViewErrorBag();
        $errors->put('default', new MessageBag([
            'email' => ['Las credenciales no son correctas.'],
        ]));

        $response = $this->withSession(['errors' => $errors])->post(route('login'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertRedirect(route('company.dashboard'));
        $response->assertSessionMissing('errors');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_do_not_authenticate_the_user(): void
    {
        User::factory()->create([
            'email' => 'company@example.com',
            'password' => 'correct-password',
            'role' => 'company',
            'active' => true,
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'company@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_company_visiting_login_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'company@example.com',
            'password' => 'correct-password',
            'role' => 'company',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('company.dashboard'));
    }

    public function test_login_page_is_not_cached_by_the_browser(): void
    {
        $response = $this->get(route('login'));

        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response->assertHeader('Pragma', 'no-cache');
    }
}
