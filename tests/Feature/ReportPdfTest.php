<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_a_pdf_report_from_the_reports_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'active' => true]);

        $this->actingAs($admin)
            ->get(route('admin.reports'))
            ->assertOk()
            ->assertSee(route('admin.reports.pdf'));

        $this->actingAs($admin)
            ->get(route('admin.reports.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=Reporte_Admin_BolsaUnipaz_'.now()->format('Y-m-d').'.pdf')
            ->assertSee('%PDF-', false);
    }

    public function test_company_can_download_its_pdf_report_from_the_reports_page(): void
    {
        $user = User::factory()->create(['role' => 'company', 'active' => true]);
        Company::create([
            'user_id' => $user->id,
            'company_name' => 'Empresa de prueba',
            'contact_person' => 'Contacto de prueba',
        ]);

        $this->actingAs($user)
            ->get(route('company.reports'))
            ->assertOk()
            ->assertSee(route('company.reports.pdf'));

        $this->actingAs($user)
            ->get(route('company.reports.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=Reporte_BolsaUnipaz_'.now()->format('Y-m-d').'.pdf')
            ->assertSee('%PDF-', false);
    }

    public function test_users_cannot_download_reports_outside_their_role(): void
    {
        $student = User::factory()->create(['role' => 'student', 'active' => true]);
        $company = User::factory()->create(['role' => 'company', 'active' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'active' => true]);

        $this->actingAs($student)
            ->get('/admin/reportes/pdf')
            ->assertForbidden();

        $this->actingAs($student)
            ->get('/empresa/reportes/pdf')
            ->assertForbidden();

        $this->actingAs($company)
            ->get('/admin/reportes/pdf')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/empresa/reportes/pdf')
            ->assertForbidden();
    }
}
