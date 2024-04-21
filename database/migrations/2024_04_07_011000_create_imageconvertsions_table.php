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
        Schema::create('image_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->foreignId('original_format')->constrained('formats');
            $table->foreignId('converted_format')->constrained('formats');
            $table->string('converted_name');
            $table->string('converted_path');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imageconvertsions');
    }
};
