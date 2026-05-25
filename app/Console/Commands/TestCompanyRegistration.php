<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestCompanyRegistration extends Command
{
    protected $signature = 'test:company-registration {email?} {company?}';
    protected $description = 'Prueba el registro de una empresa';

    public function handle()
    {
        $email = $this->argument('email') ?? 'testcompany' . time() . '@example.com';
        $companyName = $this->argument('company') ?? 'Empresa Prueba ' . now()->format('YmdHis');

        $this->info("=== Probando registro de empresa ===");
        $this->info("Email: {$email}");
        $this->info("Empresa: {$companyName}");
        $this->newLine();

        try {
            // Iniciar transacción
            DB::beginTransaction();

            // Crear usuario
            $this->info("1. Creando usuario...");
            $user = User::create([
                'name'     => 'Test Responsable',
                'email'    => $email,
                'password' => Hash::make('TestCompany2024!'),
                'role'     => 'company',
            ]);
            $this->line("   ✓ Usuario creado con ID: {$user->id}");

            // Crear empresa
            $this->info("2. Creando empresa...");
            $company = Company::create([
                'user_id'        => $user->id,
                'company_name'   => $companyName,
                'nit'            => null,
                'sector'         => 'Tecnología e informática',
                'contact_person' => 'Test Responsable',
                'phone'          => '+57 300 000 0000',
                'address'        => 'Calle 50 # 20-15',
                'description'    => 'Empresa de prueba para testing',
                'status'         => 'pending',
            ]);
            $this->line("   ✓ Empresa creada con ID: {$company->id}");

            // Verificar la relación
            $this->info("3. Verificando relaciones...");
            $retrievedCompany = Company::find($company->id);
            $this->line("   ✓ Empresa encontrada: {$retrievedCompany->company_name}");
            $this->line("   ✓ Usuario relacionado: {$retrievedCompany->user->name} ({$retrievedCompany->user->email})");

            // Verificar en la base de datos
            $this->info("4. Verificando en la base de datos...");
            $dbCompany = DB::table('companies')->where('id', $company->id)->first();
            if ($dbCompany) {
                $this->line("   ✓ Empresa encontrada en DB");
                $this->table(
                    ['Campo', 'Valor'],
                    [
                        ['id', $dbCompany->id],
                        ['user_id', $dbCompany->user_id],
                        ['company_name', $dbCompany->company_name],
                        ['status', $dbCompany->status],
                        ['created_at', $dbCompany->created_at],
                    ]
                );
            } else {
                $this->error("   ✗ Empresa no encontrada en DB!");
            }

            DB::commit();
            $this->newLine();
            $this->info("✓ Registro completado correctamente!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("✗ Error: " . $e->getMessage());
            $this->error("Stack: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
