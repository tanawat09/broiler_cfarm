<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FeedIntakeMaster;

$feedIntakes = FeedIntakeMaster::where('age', '<=', 42)->get();
echo "Sum Female: " . $feedIntakes->sum('feed_female') . "\n";
echo "Sum Male: " . $feedIntakes->sum('feed_male') . "\n";
echo "Sum AH: " . $feedIntakes->sum('feed_ah') . "\n";
