<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes for report query optimization
     */
    public function up(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            // Check if index doesn't exist before creating
            if (!$this->indexExists('sensor_data', 'sensor_data_recorded_at_index')) {
                $table->index('recorded_at', 'sensor_data_recorded_at_index');
            }
            if (!$this->indexExists('sensor_data', 'sensor_data_device_recorded_index')) {
                $table->index(['device_id', 'recorded_at'], 'sensor_data_device_recorded_index');
            }
        });

        Schema::table('weather_data', function (Blueprint $table) {
            if (!$this->indexExists('weather_data', 'weather_data_recorded_at_index')) {
                $table->index('recorded_at', 'weather_data_recorded_at_index');
            }
        });

        Schema::table('irrigation_logs', function (Blueprint $table) {
            if (!$this->indexExists('irrigation_logs', 'irrigation_logs_started_at_index')) {
                $table->index('started_at', 'irrigation_logs_started_at_index');
            }
            if (!$this->indexExists('irrigation_logs', 'irrigation_logs_device_started_index')) {
                $table->index(['device_id', 'started_at'], 'irrigation_logs_device_started_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->dropIndex('sensor_data_recorded_at_index');
            $table->dropIndex('sensor_data_device_recorded_index');
        });

        Schema::table('weather_data', function (Blueprint $table) {
            $table->dropIndex('weather_data_recorded_at_index');
        });

        Schema::table('irrigation_logs', function (Blueprint $table) {
            $table->dropIndex('irrigation_logs_started_at_index');
            $table->dropIndex('irrigation_logs_device_started_index');
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        return Schema::hasIndex($table, $index);
    }
};
