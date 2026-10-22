<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class CompanyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_company_can_register_and_is_pending_by_default()
    {
        $response = $this->post('/register/company', [
            'name' => 'Representante Test',
            'email' => 'test@empresa.com',
            'password' => 'Password123*',
            'password_confirmation' => 'Password123*',
            'company_name' => 'Empresa Test SAS',
            'sector' => 'Tecnología',
            'contact_person' => 'Juan Perez',
            'phone' => '3001234567',
            'address' => 'Calle 123',
            'description' => 'Descripción de prueba de más de diez caracteres.',
        ]);

        $response->assertRedirect('/login');
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@empresa.com',
            'role' => 'company'
        ]);

        $this->assertDatabaseHas('companies', [
            'company_name' => 'Empresa Test SAS',
            'status' => 'pending'
        ]);
    }
}