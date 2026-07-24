<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$models = [];
foreach (glob(app_path('Models/*.php')) as $file) {
    $class = 'App\\Models\\' . basename($file, '.php');
    if (class_exists($class)) {
        $model = new $class;
        $table = $model->getTable();
        $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
        $fillable = $model->getFillable();
        
        $missingInDb = array_diff($fillable, $columns);
        
        if (!empty($missingInDb)) {
            echo 'MODEL ERROR: ' . $class . ' has fillable fields not in DB: ' . implode(', ', $missingInDb) . "\n";
        } else {
            echo 'OK: ' . $class . "\n";
        }
    }
}
exit();
