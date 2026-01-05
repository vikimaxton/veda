<?php

use Illuminate\Support\Facades\Route;
use Plugins\HelloWorld\HelloWorldService;

Route::prefix('api/hello-world')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/stats', function (HelloWorldService $service) {
        return response()->json($service->getStats());
    });
});
