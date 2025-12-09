<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeederController;
use App\Http\Controllers\TemperatureController;
use App\Http\Controllers\LightController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ========== FEEDER ROUTES ==========
// Schedule Management
Route::get('/feeder/schedules', [FeederController::class, 'getSchedules']);
Route::post('/feeder/schedules', [FeederController::class, 'createSchedule']);
Route::put('/feeder/schedules/{id}', [FeederController::class, 'updateSchedule']);
Route::delete('/feeder/schedules/{id}', [FeederController::class, 'deleteSchedule']);

// Manual Control
Route::post('/feeder/feed-now', [FeederController::class, 'feedNow']);

// History & Logs
Route::get('/feeder/history', [FeederController::class, 'getHistory']);
Route::post('/feeder/log', [FeederController::class, 'logFeed']);

// Status Check (for Python bridge)
Route::get('/feeder/check-status', [FeederController::class, 'checkStatus']);

// ========== TEMPERATURE ROUTES ==========
// Settings
Route::get('/temperature/settings', [TemperatureController::class, 'getSettings']);
Route::put('/temperature/settings', [TemperatureController::class, 'updateSettings']);

// Readings
Route::post('/temperature/log', [TemperatureController::class, 'logReading']);
Route::post('/temperature/realtime', [TemperatureController::class, 'updateRealtime']); // Real-time updates
Route::get('/temperature/latest', [TemperatureController::class, 'getLatestReading']); 
Route::get('/temperature/history', [TemperatureController::class, 'getHistory']);

// Fan Status (for Python bridge)
Route::get('/temperature/fan-status', [TemperatureController::class, 'getFanStatus']);

// ========== LIGHT ROUTES ==========
Route::get('/light/status', [LightController::class, 'getStatus']); // For website
Route::post('/light/toggle', [LightController::class, 'toggle']);
Route::put('/light/status', [LightController::class, 'setStatus']);
Route::get('/light/led-status', [LightController::class, 'getLightStatus']); // For Python bridge (like fan)
