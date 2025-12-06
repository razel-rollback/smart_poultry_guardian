<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feeder_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('trigger_type', ['manual', 'scheduled'])->default('manual');
            $table->foreignId('schedule_id')->nullable()->constrained('feeder_schedules')->onDelete('set null');
            $table->timestamp('fed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeder_logs');
    }
};
