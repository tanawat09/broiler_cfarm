<?php
require __DIR__ . '/../vendor/autoload.class.php'; // wait, laravel autoload is bootstrap/autoload.php or vendor/autoload.php
require __DIR__ . '/../bootstrap/app.php';

// Wait, let's just write a proper Laravel artisan command runner or use tinker by writing a clean php script that boots laravel:
// Actually, Laravel's artisan can run a php file: php artisan db:seed or we can just boot it.
// Let's boot Laravel in our script:
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$flockId = 11;
$records = \App\Models\DailyHouseRecord::where('flock_id', $flockId)->count();
$starts = \App\Models\FlockHouseStart::where('flock_id', $flockId)->get();
$placements = \App\Models\FlockHousePlacement::where('flock_id', $flockId)->get();

echo "Records: " . $records . "\n";
echo "Starts:\n";
foreach ($starts as $s) {
    echo "  H{$s->house_id}: start_date=" . ($s->start_date ? $s->start_date->toDateString() : 'NULL') . " birds={$s->initial_birds}\n";
}
echo "Placements:\n";
foreach ($placements as $p) {
    echo "  H{$p->house_id}: placement_date=" . ($p->placement_date ? $p->placement_date->toDateString() : 'NULL') . " chicks={$p->chicks_in}\n";
}
