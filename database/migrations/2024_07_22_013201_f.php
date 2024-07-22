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
        Schema::table('video_conversions', function (Blueprint $table) {
            // Modify the frame_rate column
            $table->double('frame_rate', 2, 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_conversions', function (Blueprint $table) {
            // Reverse the changes made in the up() method
            $table->double('frame_rate')->nullable()->default(null)->change();
        });
    }
};