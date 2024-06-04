<?php

namespace Database\Seeders;

use App\Models\EbookFormat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EbookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formats = [
            'azw3',
            'epub',
            'docx',
            'fb2',
            'htmlz',
            'oeb',
            'lit',
            'lrf',
            'mobi',
            'pdb',
            'pmlz',
            'rb',
            'pdf',
            'rtf',
            'snb',
            'tcr',
            'txt',
            'txtz',
            'zip',
        ];

        foreach ($formats as $format) {
            EbookFormat::create([
                'name' => $format,
                'extension' => $format,
            ]);
        }
    }
}
