<?php

namespace App\Models;

use App\Models\ImageConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    use HasFactory;
    protected $table = 'image_formats';
    protected $fillable = [
        'id',
        'name',
        'extension',
    ];

    public function imageConversions()
    {
        return $this->hasMany(ImageConversion::class, 'original_format');
    }

    public function convertedImageConversions()
    {
        return $this->hasMany(ImageConversion::class, 'converted_format');
    }
}
