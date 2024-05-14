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
        Schema::create('archive_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->foreignId('original_format')->constrained('archive_formats');
            $table->foreignId('converted_format')->constrained('archive_formats');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->string('guid')->index();
            $table->string('converted_name');
            $table->integer('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_conversions');
    }
};
