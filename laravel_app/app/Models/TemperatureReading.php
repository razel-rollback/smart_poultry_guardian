<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemperatureReading extends Model
{
    protected $fillable = [
        'temperature',
        'humidity',
        'light_level',
        'led_state',
        'recorded_at'
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'light_level' => 'integer',
        'led_state' => 'boolean',
        'recorded_at' => 'datetime'
    ];
}
