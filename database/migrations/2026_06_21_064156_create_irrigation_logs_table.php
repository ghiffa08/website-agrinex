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
        Schema::create('irrigation_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('session_id')->unique();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->integer('success_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('valve_on_count')->default(0);
            $table->float('water_used_liters')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->string('mode')->default('auto');
            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('irrigation_logs');
    }
};
