<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imageconversion extends Model
{
    use HasFactory;
    // table = 'image_conversions';
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
    ];

    public function originalFormat()
    {
        return $this->belongsTo(Format::class, 'original_format');
    }

    public function convertedFormat()
    {
        return $this->belongsTo(Format::class, 'converted_format');
    }

    public function getConvertedPathAttribute()
    {
        return storage_path('app/images/' . $this->converted_name . '.' . $this->convertedFormat->extension);
    }

    public function getOriginalFormatAttribute()
    {
        return $this->originalFormat->extension;
    }

    public function getConvertedFormatAttribute()
    {
        return $this->convertedFormat->extension;
    }
}
