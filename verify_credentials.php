<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Usuarios actuales en BD ===\n\n";

$users = User::all(['id', 'name', 'email', 'role', 'password'])->toArray();

foreach ($users as $user) {
    echo "Email: {$user['email']}\n";
    echo "Role: {$user['role']}\n";
}

echo "\n=== Verificando contraseñas ===\n\n";

$admin = User::where('email', 'admin@unipaz.edu.co')->first();
$company = User::where('email', 'info@tecnosoluciones.edu.co')->first();
$student = User::where('email', 'carlos.lopez@unipaz.edu.co')->first();

if ($admin) {
    echo "✓ Admin encontrado\n";
    echo "  Contraseña 'AdminUNIPAZ2024*': " . (Hash::check('AdminUNIPAZ2024*', $admin->password) ? '✓ FUNCIONA' : '✗ NO') . "\n\n";
}

if ($company) {
    echo "✓ Empresa encontrada\n";
    echo "  Contraseña 'EmpresaUNIPAZ2024*': " . (Hash::check('EmpresaUNIPAZ2024*', $company->password) ? '✓ FUNCIONA' : '✗ NO') . "\n\n";
} else {
    echo "✗ Empresa NO encontrada (buscando info@tecnosoluciones.edu.co)\n\n";
}

if ($student) {
    echo "✓ Estudiante encontrado\n";
    echo "  Contraseña 'EstudianteUNIPAZ2024*': " . (Hash::check('EstudianteUNIPAZ2024*', $student->password) ? '✓ FUNCIONA' : '✗ NO') . "\n\n";
}
