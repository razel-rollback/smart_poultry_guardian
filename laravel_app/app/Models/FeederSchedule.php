<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeederSchedule extends Model
{
    protected $fillable = [
        'feed_time',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'feed_time' => 'datetime:H:i'
    ];

    public function logs()
    {
        return $this->hasMany(FeederLog::class, 'schedule_id');
    }
}
