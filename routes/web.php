<?php

use App\Http\Controllers\WorkOrderPrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/panels');
});

// Work Order Print Routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/work-order/{task}/print', [WorkOrderPrintController::class, 'show'])
        ->name('work-order.print');
});


