<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temperature_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('threshold_temperature', 5, 2)->default(30.00);
            $table->decimal('threshold_humidity', 5, 2)->default(70.00);
            $table->boolean('fan_override')->default(false); // Manual fan control
            $table->timestamps();
        });

        // Insert default setting
        DB::table('temperature_settings')->insert([
            'threshold_temperature' => 30.00,
            'threshold_humidity' => 70.00,
            'fan_override' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('temperature_settings');
    }
};
