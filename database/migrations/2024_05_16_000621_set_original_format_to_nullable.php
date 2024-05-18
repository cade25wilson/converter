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
        Schema::table('archive_conversions', function (Blueprint $table) {
            // make original_name not nullable
            $table->string('original_name')->nullable()->change();
            $table->integer('original_format')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nullable', function (Blueprint $table) {
            //
        });
    }
};
