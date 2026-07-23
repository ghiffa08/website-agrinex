<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // AI Calibration Status
            $table->enum('ai_calibration_status', [
                'idle',              // Belum pernah kalibrasi
                'user_saturating',   // User sedang siram tanah
                'waiting_24h',       // Tunggu 24 jam drainase
                'analyzing',         // AI sedang analisis
                'completed',         // Selesai
                'failed'             // Gagal (data insufficient)
            ])->default('idle')->after('threshold');
            
            // Tracking timestamps
            $table->timestamp('ai_calibration_started_at')->nullable()->after('ai_calibration_status');
            $table->timestamp('ai_saturation_completed_at')->nullable()->after('ai_calibration_started_at');
            $table->timestamp('ai_calibration_completed_at')->nullable()->after('ai_saturation_completed_at');
            
            // AI Analysis Results
            $table->integer('ai_fc_raw_value')->nullable()->after('ai_calibration_completed_at')
                ->comment('Field Capacity ADC value detected by AI');
            $table->integer('ai_wp_raw_value')->nullable()->after('ai_fc_raw_value')
                ->comment('Wilting Point ADC value detected by AI');
            $table->decimal('ai_confidence_score', 5, 2)->nullable()->after('ai_wp_raw_value')
                ->comment('AI confidence score 0-100%');
            
            // Analysis metadata (JSON for detailed insights)
            $table->json('ai_analysis_data')->nullable()->after('ai_confidence_score')
                ->comment('Detailed AI analysis results and reasoning');
            
            // Historical tracking for progressive learning
            $table->integer('ai_analysis_iteration')->default(0)->after('ai_analysis_data')
                ->comment('Number of times AI has analyzed (0=first, 1=24h, 2=48h, 3=72h)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn([
                'ai_calibration_status',
                'ai_calibration_started_at',
                'ai_saturation_completed_at',
                'ai_calibration_completed_at',
                'ai_fc_raw_value',
                'ai_wp_raw_value',
                'ai_confidence_score',
                'ai_analysis_data',
                'ai_analysis_iteration'
            ]);
        });
    }
};
