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
            $table->dropForeign(['original_format']);
            $table->unsignedBigInteger('original_format')->nullable()->change();
            $table->foreign('original_format')->references('id')->on('audio_formats');
        });

        Schema::table('spreadsheet_conversions', function (Blueprint $table) {
            $table->dropForeign(['original_format']);
            $table->unsignedBigInteger('original_format')->nullable()->change();
            $table->foreign('original_format')->references('id')->on('spreadsheet_formats');
        });

        Schema::table('video_conversions', function (Blueprint $table) {
            $table->dropForeign(['original_format']);
            $table->unsignedBigInteger('original_format')->nullable()->change();
            $table->foreign('original_format')->references('id')->on('video_formats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audioconversions', function (Blueprint $table) {
            $table->string('original_format')->nullable(false)->default(0)->change();
        });

        Schema::table('spreadsheet_conversions', function (Blueprint $table) {
            $table->string('original_format')->nullable(false)->default(0)->change();
        });

        Schema::table('video_conversions', function (Blueprint $table) {
            $table->string('original_format')->nullable(false)->default(0)->change();
        });
    }
};
