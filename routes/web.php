<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyHouseRecordController;
use App\Http\Controllers\DailyRecordImportController;
use App\Http\Controllers\ChickPriceMasterController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FeedReceiptController;
use App\Http\Controllers\FeedIntakeMasterController;
use App\Http\Controllers\FlockController;
use App\Http\Controllers\FlockCloseController;
use App\Http\Controllers\FlockSaleRecordController;
use App\Http\Controllers\FlockSummaryController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\ChickSourceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalePriceMasterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaterMeterRecordController;
use App\Http\Controllers\WeightRecordController;
use App\Http\Controllers\FlockCatchRecordController;
use App\Http\Controllers\CatchingTeamController;
use App\Support\FarmAccess;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/daily-records', function () {
        $flock = FarmAccess::activeFlockFor(request()->user());

        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนบันทึกข้อมูลประจำวัน');
        }

        return redirect()->route('flocks.daily-records.index', $flock);
    })->name('daily-records.shortcut');

    Route::get('/summary', function () {
        $flock = FarmAccess::activeFlockFor(request()->user());

        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนดูใบหน้าเล้า');
        }

        return redirect()->route('flocks.summary', $flock);
    })->name('summary.shortcut');

    Route::get('/water-meters', function () {
        $flock = FarmAccess::activeFlockFor(request()->user());

        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนบันทึกมิเตอร์น้ำ');
        }

        return redirect()->route('flocks.water-meters.index', $flock);
    })->name('water-meters.shortcut');

    Route::get('/weight-records', function () {
        $flock = FarmAccess::activeFlockFor(request()->user());

        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนบันทึกน้ำหนักไก่');
        }

        return redirect()->route('flocks.weight-records.index', $flock);
    })->name('weight-records.shortcut');

    Route::get('/sale-records', function () {
        $flock = FarmAccess::activeFlockFor(request()->user());

        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนบันทึกจับไก่ขาย');
        }

        return redirect()->route('flocks.sale-records.index', $flock);
    })->name('sale-records.shortcut');

    Route::get('/catch-records', [FlockCatchRecordController::class, 'shortcut'])->name('catch-records.shortcut');

    Route::get('/flock-close', function () {
        $flock = FarmAccess::activeFlockFor(request()->user());

        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนปิดรุ่น');
        }

        return redirect()->route('flocks.close.show', $flock);
    })->name('flock-close.shortcut');

    Route::get('/farms/{farm}/houses', [HouseController::class, 'index'])->name('farms.houses.index');
    Route::post('/farms/{farm}/houses/generate', [HouseController::class, 'generate'])->name('farms.houses.generate');
    Route::put('/farms/{farm}/houses/{house}', [HouseController::class, 'update'])->name('farms.houses.update');

    Route::resource('farms', FarmController::class);
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('chick-sources', ChickSourceController::class)->except(['show']);
    Route::resource('catching-teams', CatchingTeamController::class)->except(['show']);
    Route::resource('chick-price-masters', ChickPriceMasterController::class)->only(['index', 'store', 'destroy']);
    Route::resource('sale-price-masters', SalePriceMasterController::class)->only(['index', 'store', 'destroy']);
    Route::resource('feed-intake-masters', FeedIntakeMasterController::class)->only(['index', 'store', 'destroy']);
    Route::resource('feed-price-masters', \App\Http\Controllers\FeedPriceMasterController::class)->only(['index', 'store', 'destroy']);
    Route::resource('feed-receipts', FeedReceiptController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('/flocks/{flock}/sale-records', FlockSaleRecordController::class)
        ->names('flocks.sale-records')
        ->except(['show']);

    Route::resource('/flocks/{flock}/catch-records', FlockCatchRecordController::class)
        ->names('flocks.catch-records')
        ->except(['show']);
    Route::get('/flocks/{flock}/close', [FlockCloseController::class, 'show'])->name('flocks.close.show');
    Route::post('/flocks/{flock}/close', [FlockCloseController::class, 'close'])->name('flocks.close.store');
    Route::post('/flocks/{flock}/unlock', [FlockCloseController::class, 'unlock'])->name('flocks.close.unlock');
    Route::get('/flocks/{flock}/daily-records', [DailyHouseRecordController::class, 'index'])->name('flocks.daily-records.index');
    Route::post('/flocks/{flock}/daily-records', [DailyHouseRecordController::class, 'store'])->name('flocks.daily-records.store');
    Route::get('/flocks/{flock}/daily-records/{date}', [DailyHouseRecordController::class, 'show'])->name('flocks.daily-records.show');
    Route::get('/daily-records/import', [DailyRecordImportController::class, 'showImportPage'])->name('daily-records.import-page');
    Route::post('/daily-records/import', [DailyRecordImportController::class, 'handleImport'])->name('daily-records.import-submit');
    Route::get('/flocks/{flock}/water-meters', [WaterMeterRecordController::class, 'index'])->name('flocks.water-meters.index');
    Route::post('/flocks/{flock}/water-meters', [WaterMeterRecordController::class, 'store'])->name('flocks.water-meters.store');
    Route::get('/flocks/{flock}/summary', FlockSummaryController::class)->name('flocks.summary');
    Route::get('/flocks/{flock}/losses', \App\Http\Controllers\FlockLossReportController::class)->name('flocks.losses');
    Route::get('/losses', function () {
        $flock = FarmAccess::activeFlockFor(request()->user());
        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนดูรายงานการสูญเสียรายวัน');
        }
        return redirect()->route('flocks.losses', $flock);
    })->name('losses.shortcut');
    Route::get('/flocks/{flock}/feed-summary', \App\Http\Controllers\FlockFeedSummaryController::class)->name('flocks.feed-summary');
    Route::get('/feed-summary', function () {
        $flock = FarmAccess::activeFlockFor(request()->user());
        if (! $flock) {
            return redirect()
                ->route('flocks.index')
                ->with('status', 'กรุณาเปิดรุ่นการเลี้ยงก่อนดูสรุปการใช้อาหาร');
        }
        return redirect()->route('flocks.feed-summary', $flock);
    })->name('feed-summary.shortcut');
    Route::get('/flocks/{flock}/placements', [\App\Http\Controllers\FlockPlacementController::class, 'index'])->name('flocks.placements.index');
    Route::post('/flocks/{flock}/placements', [\App\Http\Controllers\FlockPlacementController::class, 'store'])->name('flocks.placements.store');
    Route::get('/placements', [\App\Http\Controllers\FlockPlacementController::class, 'shortcut'])->name('placements.shortcut');
    Route::get('/placements/create-flock', [\App\Http\Controllers\FlockPlacementController::class, 'createFlockForm'])->name('placements.create-flock-form');
    Route::post('/placements/create-flock', [\App\Http\Controllers\FlockPlacementController::class, 'createFlock'])->name('placements.create-flock');

    Route::get('/flocks/{flock}/weight-records', [WeightRecordController::class, 'index'])->name('flocks.weight-records.index');
    Route::post('/flocks/{flock}/weight-records', [WeightRecordController::class, 'store'])->name('flocks.weight-records.store');
    Route::resource('flocks', FlockController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
