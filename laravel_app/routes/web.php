<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('control_panel');
});

Route::get('/dashboard', function () {
    return view('control_panel');
});

