<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemperatureSetting extends Model
{
    protected $fillable = [
        'threshold_temperature',
        'fan_override'
    ];

    protected $casts = [
        'threshold_temperature' => 'decimal:2',
        'fan_override' => 'boolean'
    ];
}
