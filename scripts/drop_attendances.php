<?php

// Bootstrap the framework and drop the 'attendances' table if it exists
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

Schema::dropIfExists('attendances');
echo "dropped attendances if it existed\n";
