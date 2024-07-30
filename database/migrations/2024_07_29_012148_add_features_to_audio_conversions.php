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
        Schema::table('audioconversions', function (Blueprint $table) {
            $table->boolean('reverse_audio')->default(false);
            $table->double('audio', 2, 2)->nullable(true)->default(null);
            $table->boolean('fade_in')->default(false);
            $table->boolean('fade_out')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_conversions', function (Blueprint $table) {
            //
        });
    }
};
