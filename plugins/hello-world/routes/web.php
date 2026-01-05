<?php

use Illuminate\Support\Facades\Route;
use Plugins\HelloWorld\HelloWorldService;

Route::prefix('hello-world')->group(function () {
    Route::get('/', function (HelloWorldService $service) {
        return response()->json([
            'message' => $service->getGreeting(),
            'stats' => $service->getStats(),
        ]);
    });

    Route::get('/greet/{name}', function (string $name, HelloWorldService $service) {
        return response()->json([
            'message' => $service->getGreeting($name),
        ]);
    });
});
