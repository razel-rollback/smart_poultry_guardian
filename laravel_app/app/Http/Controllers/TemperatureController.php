<?php

namespace App\Http\Controllers;

use App\Models\TemperatureSetting;
use App\Models\TemperatureReading;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TemperatureController extends Controller
{
    // Get current settings
    public function getSettings()
    {
        $settings = TemperatureSetting::first();
        
        if (!$settings) {
            $settings = TemperatureSetting::create([
                'threshold_temperature' => 30.00,
                'fan_override' => false
            ]);
        }

        return response()->json($settings);
    }

    // Update settings
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'threshold_temperature' => 'sometimes|numeric|min:0|max:100',
            'fan_override' => 'sometimes|boolean'
        ]);

        $settings = TemperatureSetting::first();
        
        if (!$settings) {
            $settings = TemperatureSetting::create($validated);
        } else {
            $settings->update($validated);
        }

        return response()->json($settings);
    }

    public function logReading(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'light_level' => 'nullable|numeric'
        ]);

        $reading = TemperatureReading::create([
            'temperature' => $validated['temperature'],
            'humidity' => $validated['humidity'],
            'recorded_at' => Carbon::now()
        ]);

        return response()->json($reading, 201);
    }

    // Update real-time reading (called every 2 seconds by Python bridge)
    public function updateRealtime(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'light_level' => 'nullable|numeric'
        ]);

        // Store in cache for 10 seconds (fast access)
        cache()->put('realtime_sensor', [
            'temperature' => $validated['temperature'],
            'humidity' => $validated['humidity'],
            'light_level' => $validated['light_level'] ?? null,
            'recorded_at' => Carbon::now()
        ], 10);

        return response()->json(['status' => 'ok'], 200);
    }

    // Get latest reading (for real-time display)
    public function getLatestReading()
    {
        // First check cache for real-time data
        $realtimeData = cache()->get('realtime_sensor');
        
        if ($realtimeData) {
            return response()->json($realtimeData);
        }

        // Fallback to database
        $reading = TemperatureReading::latest('recorded_at')->first();

        return response()->json($reading);
    }

    // Get historical readings
    public function getHistory(Request $request)
    {
        $hours = $request->query('hours', 24);
        
        $readings = TemperatureReading::where('recorded_at', '>=', Carbon::now()->subHours($hours))
            ->orderBy('recorded_at', 'desc')
            ->get();

        return response()->json($readings);
    }

    // Check if fan should be on (called by Python bridge)
    public function getFanStatus()
    {
        $settings = TemperatureSetting::first();
        $latestReading = TemperatureReading::latest('recorded_at')->first();

        $fanOn = false;

        if ($settings) {
            // If manual override is on, fan is always on
            if ($settings->fan_override) {
                $fanOn = true;
            } 
            // Otherwise, check if temperature exceeds threshold
            elseif ($latestReading && $latestReading->temperature > $settings->threshold_temperature) {
                $fanOn = true;
            }
        }

        return response()->json([
            'fan_status' => $fanOn ? 'ON' : 'OFF',
            'current_temperature' => $latestReading ? $latestReading->temperature : null,
            'threshold' => $settings ? $settings->threshold_temperature : null,
            'override' => $settings ? $settings->fan_override : false
        ]);
    }
}
