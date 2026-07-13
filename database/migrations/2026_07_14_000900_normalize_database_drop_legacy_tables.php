<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drop legacy tables that are no longer used and add proper foreign key constraints
     */
    public function up(): void
    {
        // Drop stored procedures
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cleanup_old_data');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_session_details');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_statistics');
        
        // Drop views
        DB::statement('DROP VIEW IF EXISTS v_daily_stats');
        DB::statement('DROP VIEW IF EXISTS v_latest_sessions');
        DB::statement('DROP VIEW IF EXISTS v_node_activity');
        
        // Drop legacy tables (old system, no longer used by ESP32)
        Schema::dropIfExists('node_logs');
        Schema::dropIfExists('sensor_node_data');
        Schema::dropIfExists('sensor_weather_data');
        Schema::dropIfExists('json_backup');
        Schema::dropIfExists('getdata_logs');
        Schema::dropIfExists('irrigate_logs');
        Schema::dropIfExists('node');
        Schema::dropIfExists('push_logs');
        Schema::dropIfExists('data_sync_status');
        
        // Add foreign key constraints to active tables
        Schema::table('devices', function (Blueprint $table) {
            if (!$this->foreignKeyExists('devices', 'devices_user_id_foreign')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!$this->foreignKeyExists('devices', 'devices_lahan_pantau_id_foreign')) {
                $table->foreign('lahan_pantau_id')->references('id')->on('lahan_pantaus')->onDelete('set null');
            }
        });
        
        Schema::table('sensor_data', function (Blueprint $table) {
            if (!$this->foreignKeyExists('sensor_data', 'sensor_data_device_id_foreign')) {
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('sensor_data', 'sensor_data_data_session_id_foreign')) {
                $table->foreign('data_session_id')->references('id')->on('data_sessions')->onDelete('set null');
            }
        });
        
        Schema::table('weather_data', function (Blueprint $table) {
            if (!$this->foreignKeyExists('weather_data', 'weather_data_device_id_foreign')) {
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            }
        });
        
        Schema::table('device_logs', function (Blueprint $table) {
            if (!$this->foreignKeyExists('device_logs', 'device_logs_device_id_foreign')) {
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            }
        });
        
        Schema::table('valve_logs', function (Blueprint $table) {
            if (!$this->foreignKeyExists('valve_logs', 'valve_logs_device_id_foreign')) {
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('valve_logs', 'valve_logs_irrigation_log_id_foreign')) {
                $table->foreign('irrigation_log_id')->references('id')->on('irrigation_logs')->onDelete('cascade');
            }
        });
        
        // Add indexes for performance
        Schema::table('sensor_data', function (Blueprint $table) {
            if (!$this->indexExists('sensor_data', 'sensor_data_device_id_recorded_at_index')) {
                $table->index(['device_id', 'recorded_at']);
            }
        });
        
        Schema::table('device_logs', function (Blueprint $table) {
            if (!$this->indexExists('device_logs', 'device_logs_device_id_logged_at_index')) {
                $table->index(['device_id', 'logged_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys
        Schema::table('valve_logs', function (Blueprint $table) {
            $table->dropForeign(['irrigation_log_id']);
            $table->dropForeign(['device_id']);
        });
        
        Schema::table('device_logs', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropIndex(['device_id', 'logged_at']);
        });
        
        Schema::table('weather_data', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
        });
        
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->dropForeign(['data_session_id']);
            $table->dropForeign(['device_id']);
            $table->dropIndex(['device_id', 'recorded_at']);
        });
        
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['lahan_pantau_id']);
            $table->dropForeign(['user_id']);
        });
        
        // Note: We don't recreate the legacy tables in down() 
        // because this is a one-way normalization migration
        // If rollback is needed, restore from backup
    }
    
    /**
     * Check if a foreign key exists
     */
    private function foreignKeyExists(string $table, string $key): bool
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        $exists = DB::select(
            "SELECT CONSTRAINT_NAME 
             FROM information_schema.TABLE_CONSTRAINTS 
             WHERE TABLE_SCHEMA = ? 
             AND TABLE_NAME = ? 
             AND CONSTRAINT_NAME = ? 
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$dbName, $table, $key]
        );
        
        return count($exists) > 0;
    }
    
    /**
     * Check if an index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        $exists = DB::select(
            "SELECT INDEX_NAME 
             FROM information_schema.STATISTICS 
             WHERE TABLE_SCHEMA = ? 
             AND TABLE_NAME = ? 
             AND INDEX_NAME = ?",
            [$dbName, $table, $index]
        );
        
        return count($exists) > 0;
    }
};
