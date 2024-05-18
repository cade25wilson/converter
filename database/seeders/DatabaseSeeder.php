<?php

namespace Database\Seeders;

use App\Models\ConversionTypes;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spiral\RoadRunner\Console\Archive\Archive;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ArchiveSeeder::class);
        $this->call(AudioSeeder::class);
        $this->call(ConversionTypesSeeder::class);
        $this->call(FormatSeeder::class);
        $this->call(SpreadsheetSeeder::class);
        $this->call(VideoSeeder::class);
    }
}
