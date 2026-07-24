<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$router = app("router");
$routes = $router->getRoutes();

$success = 0;
$failed = 0;

foreach ($routes as $route) {
    if (in_array("GET", $route->methods()) && !str_contains($route->uri(), "{") && !str_contains($route->uri(), "storage")) {
        $req = Illuminate\Http\Request::create($route->uri(), "GET");
        try {
            $res = app()->make(Illuminate\Contracts\Http\Kernel::class)->handle($req);
            $status = $res->getStatusCode();
            if ($status >= 500) {
                echo "FAIL: " . $route->uri() . " (Status: " . $status . ")\n";
                $failed++;
            } else {
                $success++;
            }
        } catch (\Exception $e) {
            echo "EXCEPTION: " . $route->uri() . " (" . $e->getMessage() . ")\n";
            $failed++;
        }
    }
}
echo "\nRoute Check Complete. Success: $success, Failed: $failed\n";

