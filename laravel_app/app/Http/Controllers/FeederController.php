<?php

namespace App\Http\Controllers;

use App\Models\FeederSchedule;
use App\Models\FeederLog;
use App\Models\FeederCommand;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeederController extends Controller
{
    // Get all schedules
    public function getSchedules()
    {
        $schedules = FeederSchedule::orderBy('feed_time')->get();
        return response()->json($schedules);
    }

    // Create new schedule
    public function createSchedule(Request $request)
    {
        $validated = $request->validate([
            'feed_time' => 'required|date_format:H:i'
        ]);

        $schedule = FeederSchedule::create([
            'feed_time' => $validated['feed_time'],
            'is_active' => true
        ]);

        return response()->json($schedule, 201);
    }

    // Update schedule
    public function updateSchedule(Request $request, $id)
    {
        $schedule = FeederSchedule::findOrFail($id);
        
        $validated = $request->validate([
            'feed_time' => 'sometimes|date_format:H:i',
            'is_active' => 'sometimes|boolean'
        ]);

        $schedule->update($validated);

        return response()->json($schedule);
    }

    // Delete schedule
    public function deleteSchedule($id)
    {
        $schedule = FeederSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }

    // Manual feed command (from website)
    public function feedNow()
    {
        // Clean up old completed commands (keep last 100)
        $oldCommandsCount = FeederCommand::where('status', 'completed')->count();
        if ($oldCommandsCount > 100) {
            FeederCommand::where('status', 'completed')
                ->orderBy('id', 'asc')
                ->limit($oldCommandsCount - 100)
                ->delete();
        }
        
        $command = FeederCommand::create(['status' => 'pending']);

        return response()->json([
            'message' => 'Feed command sent',
            'command_id' => $command->id
        ]);
    }

    // Log feed action (called by Python bridge)
    public function logFeed(Request $request)
    {
        $scheduleId = $request->input('schedule_id');
        $commandId = $request->input('command_id');
        $triggerType = $request->input('trigger_type'); // Allow explicit trigger type

        // Determine trigger type
        if ($triggerType) {
            // Explicitly set by Python bridge
            $type = $triggerType;
        } elseif ($scheduleId) {
            // Has schedule_id
            $type = 'scheduled';
        } elseif ($commandId) {
            // Has command_id (manual)
            $type = 'manual';
        } else {
            // Default to manual if nothing specified
            $type = 'manual';
        }

        $log = FeederLog::create([
            'trigger_type' => $type,
            'schedule_id' => $scheduleId,
            'fed_at' => Carbon::now()
        ]);

        // Mark command as completed if it's a manual feed
        if ($commandId) {
            FeederCommand::where('id', $commandId)
                ->update(['status' => 'completed']);
        }

        return response()->json([
            'success' => true,
            'log' => $log
        ], 201);
    }

    // Get feed history
    public function getHistory(Request $request)
    {
        $days = $request->query('days', 7);
        
        $logs = FeederLog::with('schedule')
            ->where('fed_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('fed_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    // Check status (called by Python bridge every second)
    public function checkStatus()
    {
        // Get pending manual feed command
        $pendingCommand = FeederCommand::where('status', 'pending')->first();
        
        // Mark as completed immediately to prevent repeated execution
        if ($pendingCommand) {
            $pendingCommand->update(['status' => 'completed']);
        }
        
        // Get active schedules
        $schedules = FeederSchedule::where('is_active', true)
            ->pluck('feed_time')
            ->map(function($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        return response()->json([
            'feed_command' => $pendingCommand ? 'FEED' : 'NONE',
            'command_id' => $pendingCommand ? $pendingCommand->id : null,
            'schedules' => $schedules
        ]);
        
    }
}
