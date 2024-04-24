<?php

namespace Database\Seeders;

use App\Models\SpreadsheetFormat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpreadsheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formats = [
            'XLS',
            'XLSX',
            'ODS',
            'CSV',
            'HTML',
        ];

        foreach($formats as $format) {
            SpreadsheetFormat::create([
                'extension' => strtolower($format),
                'name' => $format,
            ]);
        }
    }
}
