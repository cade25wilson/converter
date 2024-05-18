<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveConversion extends Model
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
}
