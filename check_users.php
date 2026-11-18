<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Usuarios en BD ===\n\n";

$users = \App\Models\User::all();

if ($users->isEmpty()) {
    echo "No hay usuarios en la BD\n";
} else {
    echo "Total: {$users->count()} usuarios\n\n";
    foreach ($users as $user) {
        echo "- {$user->name}\n";
        echo "  Email: {$user->email}\n";
        echo "  Role: {$user->role}\n";
        echo "  Activo: " . ($user->active ? 'Sí' : 'No') . "\n";
        echo "  Has password: " . (!is_null($user->password) ? 'Sí' : 'No') . "\n";
        echo "\n";
    }
}
