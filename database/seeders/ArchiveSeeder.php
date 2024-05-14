<?php

namespace Database\Seeders;

use App\Models\ArchiveFormat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArchiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formats = [
            "7z",
            "zip",
            "gzip",
            "bzip2",
            "tar",
            "wim",
            "xz",
        ];
    
        foreach ($formats as $format) {
            ArchiveFormat::create([
                'name' => strtoupper($format),
                'extension' => $format,
            ]);
        }
    }
}
