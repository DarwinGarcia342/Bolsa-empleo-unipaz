<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Verificando contraseñas ===\n\n";

$admin = User::where('email', 'admin@unipaz.edu.co')->first();
$company = User::where('email', 'info@tecnosoluciones.com')->first();
$student = User::where('email', 'carlos.lopez@unipaz.edu.co')->first();

echo "1. Admin (admin@unipaz.edu.co)\n";
if ($admin) {
    echo "   Password en BD: " . (strlen($admin->password) > 30 ? 'HASH: ' . substr($admin->password, 0, 30) . '...' : 'NULL') . "\n";
    echo "   Probando con 'AdminUNIPAZ2024*': " . (Hash::check('AdminUNIPAZ2024*', $admin->password) ? '✓ FUNCIONA' : '✗ NO FUNCIONA') . "\n";
} else {
    echo "   Usuario no encontrado\n";
}

echo "\n2. Empresa (info@tecnosoluciones.com)\n";
if ($company) {
    echo "   Password en BD: " . (strlen($company->password) > 30 ? 'HASH: ' . substr($company->password, 0, 30) . '...' : 'NULL') . "\n";
    echo "   Probando con 'EmpresaUNIPAZ2024*': " . (Hash::check('EmpresaUNIPAZ2024*', $company->password) ? '✓ FUNCIONA' : '✗ NO FUNCIONA') . "\n";
} else {
    echo "   Usuario no encontrado\n";
}

echo "\n3. Estudiante (carlos.lopez@unipaz.edu.co)\n";
if ($student) {
    echo "   Password en BD: " . (strlen($student->password) > 30 ? 'HASH: ' . substr($student->password, 0, 30) . '...' : 'NULL') . "\n";
    echo "   Probando con 'EstudianteUNIPAZ2024*': " . (Hash::check('EstudianteUNIPAZ2024*', $student->password) ? '✓ FUNCIONA' : '✗ NO FUNCIONA') . "\n";
} else {
    echo "   Usuario no encontrado\n";
}
