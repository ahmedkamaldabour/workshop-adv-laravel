<?php

use App\Http\Controllers\Api\MaintenanceRequestController;
use App\Http\Controllers\Api\TripRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/maintenance/request', [MaintenanceRequestController::class, 'store']);

Route::post('/trip/request', [TripRequestController::class, 'store']);