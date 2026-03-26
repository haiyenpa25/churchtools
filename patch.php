<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

Schema::disableForeignKeyConstraints();
Schema::dropIfExists('bl_entity_events'); // Rác từ code cũ
Schema::dropIfExists('bl_events'); // Cấu trúc cũ thiếu image_url
Schema::enableForeignKeyConstraints();

echo "Cleaned up legacy tables.\n";
