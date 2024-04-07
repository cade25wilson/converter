<?php

namespace App\Models;

use App\Models\ImageConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'extension',
    ];

    public function imageConversions()
    {
        return $this->hasMany(ImageConversion::class, 'original_format');
    }
}
