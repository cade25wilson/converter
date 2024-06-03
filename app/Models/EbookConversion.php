<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EbookConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'converted_format',
        'converted_name',
        'status',
        'guid',
        'file_size',
    ];
}
