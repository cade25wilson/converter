<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audioconversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'original_format',
        'converted_format',
        'converted_name',
        'status',
        'guid'
    ];

    public function originalFormat()
    {
        return $this->belongsTo(AudioFormats::class, 'original_format');
    }

    public function convertedFormat()
    {
        return $this->belongsTo(AudioFormats::class, 'converted_format');
    }
}
