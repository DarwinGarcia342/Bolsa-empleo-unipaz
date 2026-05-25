<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\Company;
use App\Models\User;

// Contar registros
$userCount = User::count();
$companyCount = Company::count();

echo "=== Estado de la Base de Datos ===\n\n";
echo "Total de usuarios: $userCount\n";
echo "Total de empresas: $companyCount\n\n";

if ($userCount > 0) {
    echo "Últimos usuarios:\n";
    User::latest()->take(3)->get()->each(function($u) {
        echo "  - {$u->name} ({$u->email}) [Rol: {$u->role}]\n";
    });
}

if ($companyCount > 0) {
    echo "\nÚltimas empresas:\n";
    Company::latest()->take(5)->get()->each(function($c) {
        echo "  - {$c->company_name} (ID: {$c->id}, Status: {$c->status}, NIT: {$c->nit})\n";
    });
} else {
    echo "\n⚠️  No hay empresas registradas en la base de datos.\n";
}

echo "\n=== Verificación de constraints ===\n";
echo "Validación de NIT: verifique que no esté duplicado\n";
echo "Validación de email: verifique dominio @unipaz.edu.co\n";
?>
