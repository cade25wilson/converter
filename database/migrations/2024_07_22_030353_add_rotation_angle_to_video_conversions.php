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
            $table->enum('rotation_angle', [90, 180, 270])->nullable()->default(null)->after('converted_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_conversions', function (Blueprint $table) {
            //
        });
    }
};
