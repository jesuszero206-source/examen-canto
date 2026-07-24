<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = new \Illuminate\Http\Request();
$req->merge([
    'fecha' => '2026-07-25',
    'hora' => '18:00',
    'personas' => 4,
    'ubicacion_preferida' => 'interior'
]);

$c = new \App\Http\Controllers\ReservaClienteController();
$res = $c->checkDisponibilidad($req);
echo $res->getContent();
