<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ebook_format extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'extension',
    ];
}
