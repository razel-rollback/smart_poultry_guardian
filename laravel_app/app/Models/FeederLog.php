<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeederLog extends Model
{
    protected $fillable = [
        'trigger_type',
        'schedule_id',
        'fed_at'
    ];

    protected $casts = [
        'fed_at' => 'datetime'
    ];

    public function schedule()
    {
        return $this->belongsTo(FeederSchedule::class, 'schedule_id');
    }
}
