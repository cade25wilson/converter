<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imageconversion extends Model
{
    use HasFactory;
    protected $table = 'image_conversions';

    protected $fillable = [
        'original_name',
        'original_format',
        'converted_format',
        'converted_name',
        'status',
        'width',
        'height',
        'watermark',
        'guid',
        'file_size',
        'strip_metadata',
        'quality',
    ];

}
