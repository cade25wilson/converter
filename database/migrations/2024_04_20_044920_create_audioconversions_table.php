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
        Schema::create('audioconversions', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->foreignId('original_format')->constrained('audio_formats');
            $table->foreignId('converted_format')->constrained('audio_formats');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->timestamps();
            $table->string('guid')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audioconversions');
    }
};
