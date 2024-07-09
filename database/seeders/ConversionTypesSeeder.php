<?php

namespace Database\Seeders;

use App\Models\ConversionTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConversionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'archive',
            'audio',
            'ebook',
            'image',
            'spreadsheet',
            'video',
        ];

        foreach ($types as $type) {
            ConversionTypes::create([
                'name' => $type,
            ]);
        }
    }
}
