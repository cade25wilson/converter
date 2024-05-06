<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoConversion extends Model
{
    use HasFactory;

    protected $fillable = ['original_name', 'original_format', 'converted_format', 'status', 'guid', 'converted_name', 'file_size'];

    public function originalFormat()
    {
        return $this->belongsTo(VideoFormat::class, 'original_format');
    }

    public function convertedFormat()
    {
        return $this->belongsTo(VideoFormat::class, 'converted_format');
    }
}
