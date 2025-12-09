<?php

namespace App\Http\Controllers;

use App\Models\LightControl;
use Illuminate\Http\Request;

class LightController extends Controller
{
    // Get light status
    public function getStatus()
    {
        $light = LightControl::first();
        
        if (!$light) {
            $light = LightControl::create(['is_on' => false]);
        }

        return response()->json($light);
    }

    // Toggle light on/off
    public function toggle()
    {
        $light = LightControl::first();
        
        if (!$light) {
            $light = LightControl::create(['is_on' => true]);
        } else {
            $light->update(['is_on' => !$light->is_on]);
        }

        return response()->json([
            'message' => 'Light toggled',
            'is_on' => $light->is_on
        ]);
    }

    // Set light status directly
    public function setStatus(Request $request)
    {
        $validated = $request->validate([
            'is_on' => 'required|boolean'
        ]);

        $light = LightControl::first();
        
        if (!$light) {
            $light = LightControl::create($validated);
        } else {
            $light->update($validated);
        }

        return response()->json($light);
    }

    // Get calculated LED status (called by Python bridge - SAME AS FAN LOGIC)
    public function getLightStatus()
    {
        // Get latest sensor data from cache
        $realtimeData = cache()->get('realtime_sensor');
        $light = LightControl::first();
        
        if (!$light) {
            $light = LightControl::create(['is_on' => false]);
        }

        $ledOn = false;

        // If manual override is enabled, use the database value
        if ($light->is_on) {
            $ledOn = true;
        }
        // Otherwise, calculate based on light sensor reading
        elseif ($realtimeData && isset($realtimeData['light_level'])) {
            $lightLevel = $realtimeData['light_level'];
            // DARK (< 400) = LED ON, BRIGHT (> 500) = LED OFF
            if ($lightLevel < 400) {
                $ledOn = true;
            } elseif ($lightLevel > 500) {
                $ledOn = false;
            }
            // Between 400-500: Keep current state (hysteresis)
        }

        return response()->json([
            'led_status' => $ledOn ? 'ON' : 'OFF',
            'light_level' => $realtimeData['light_level'] ?? null,
            'manual_override' => $light->is_on
        ]);
    }
}
