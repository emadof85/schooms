<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::select("SHOW COLUMNS FROM attendances");
foreach($cols as $c) {
    echo $c->Field . " \t " . $c->Type . " \t " . $c->Null . " \t " . ($c->Key?:'-') . " \t " . ($c->Extra?:'-') . "\n";
}
