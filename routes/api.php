<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ColorTypeController;

Route::prefix('v1')->group(function () {

    Route::post('/color-type/determinate', [ColorTypeController::class, 'detectColorType']);

    Route::get('/color-type', [ColorTypeController::class, 'getColorTypes']);

});
