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
        if (Schema::hasTable('node')) {
            Schema::table('node', function (Blueprint $table) {
                $table->decimal('fc_target', 5, 2)->nullable()->after('keterangan');
                $table->decimal('threshold', 5, 2)->nullable()->after('fc_target');
                $table->string('image_url')->nullable()->after('threshold');
            });
        }

        if (Schema::hasTable('devices')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->decimal('fc_target', 5, 2)->nullable()->after('keterangan');
                $table->decimal('threshold', 5, 2)->nullable()->after('fc_target');
                $table->string('image_url')->nullable()->after('threshold');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('node')) {
            Schema::table('node', function (Blueprint $table) {
                $table->dropColumn(['fc_target', 'threshold', 'image_url']);
            });
        }

        if (Schema::hasTable('devices')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->dropColumn(['fc_target', 'threshold', 'image_url']);
            });
        }
    }
};
