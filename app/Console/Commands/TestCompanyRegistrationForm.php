<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class TestCompanyRegistrationForm extends Command
{
    protected $signature = 'test:company-registration-form';
    protected $description = 'Prueba el registro de empresa simulando el formulario web completo';

    public function handle()
    {
        $this->info("=== Prueba de registro de empresa (formulario completo) ===");
        $this->newLine();

        // Generar NIT único para evitar conflictos de unique
        $nitNumber = rand(100000000, 999999999);
        $formattedNit = substr($nitNumber, 0, 3) . '.' . substr($nitNumber, 3, 6) . '.' . substr($nitNumber, 9, 2) . '-' . rand(0, 9);

        // Datos que simularían el formulario web
        $formData = [
            'name'              => 'Juan Carlos Pérez',
            'email'             => 'juan.perez' . time() . '@tecnosoluciones.com',
            'password'          => 'TecnoSoluciones2024!',
            'password_confirmation' => 'TecnoSoluciones2024!',
            'company_name'      => 'Tecnosol Solutions SAS',
            'nit'               => $formattedNit, // NIT con formato (como lo envía el formulario)
            'sector'            => 'Tecnología e informática',
            'contact_person'    => 'Juan Carlos Pérez García',
            'phone'             => '+57 300 500 2000',
            'address'           => 'Calle 50 # 20-15, Barrancabermeja',
            'description'       => 'Empresa especializada en soluciones tecnológicas innovadoras para el sector energético y petroquímico.',
        ];

        $this->info("Datos del formulario:");
        $this->table(['Campo', 'Valor'], array_map(fn($k, $v) => [$k, strlen($v) > 50 ? substr($v, 0, 47) . '...' : $v], array_keys($formData), $formData));
        $this->newLine();

        try {
            // Simular validación
            $this->info("1. Validando datos...");
            $rules = [
                'name'         => 'required|string|max:255',
                'email'        => 'required|email|unique:users,email',
                'password'     => 'required|string|min:8|confirmed',
                'company_name' => 'required|string|max:255',
                'nit'          => ['nullable', 'regex:/^[0-9.\-]*$/', 'unique:companies,nit'],
                'sector'       => 'required|string|max:100',
                'contact_person' => 'required|string|max:255',
                'phone'        => 'required|string|max:20',
                'address'      => 'required|string|max:255',
                'description'  => 'required|string|min:10|max:1000',
            ];

            $validator = \Illuminate\Support\Facades\Validator::make($formData, $rules);

            if ($validator->fails()) {
                $this->error("   ✗ Validación fallida:");
                foreach ($validator->errors()->all() as $error) {
                    $this->line("      - {$error}");
                }
                $this->newLine();
                $this->warn("Nota: El NIT tiene formato '900.123.456-7' pero la regla es 'numeric'");
                $this->warn("      Necesita ser sin puntos para pasar validación: '9001234567'");
                return 1;
            }
            $this->line("   ✓ Validación pasada correctamente");

            // Crear usuario
            $this->info("2. Creando usuario...");
            DB::beginTransaction();

            $user = User::create([
                'name'     => $formData['name'],
                'email'    => $formData['email'],
                'password' => Hash::make($formData['password']),
                'role'     => 'company',
            ]);
            $this->line("   ✓ Usuario creado (ID: {$user->id})");

            // Crear empresa
            $this->info("3. Creando empresa...");
            $company = Company::create([
                'user_id'        => $user->id,
                'company_name'   => $formData['company_name'],
                'nit'            => $formData['nit'], // Puede ser null ahora
                'sector'         => $formData['sector'],
                'contact_person' => $formData['contact_person'],
                'phone'          => $formData['phone'],
                'address'        => $formData['address'],
                'description'    => $formData['description'],
                'status'         => 'pending',
            ]);
            $this->line("   ✓ Empresa creada (ID: {$company->id})");

            // Verificar en BD
            $this->info("4. Verificando en base de datos...");
            $dbUser = DB::table('users')->where('email', $formData['email'])->first();
            $dbCompany = DB::table('companies')->where('id', $company->id)->first();

            if ($dbUser && $dbCompany) {
                $this->line("   ✓ Usuario y Empresa guardados correctamente en BD");
                $this->newLine();
                $this->table(
                    ['Entidad', 'ID', 'Estado'],
                    [
                        ['Usuario', $dbUser->id, "✓ {$dbUser->email}"],
                        ['Empresa', $dbCompany->id, "✓ {$dbCompany->company_name}"],
                        ['Estado', 'pending', '✓ Esperando aprobación'],
                    ]
                );
            }

            DB::commit();
            $this->newLine();
            $this->info("✓ Prueba completada exitosamente!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("✗ Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
