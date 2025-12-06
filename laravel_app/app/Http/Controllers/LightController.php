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
}
