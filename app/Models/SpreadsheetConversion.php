<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpreadsheetConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'original_format',
        'converted_format',
        'status',
        'guid',
        'converted_name',
        'file_size',
    ];

    public function originalFormat()
    {
        return $this->belongsTo(SpreadsheetFormat::class, 'original_format');
    }

    public function convertedFormat()
    {
        return $this->belongsTo(SpreadsheetFormat::class, 'converted_format');
    }
}
