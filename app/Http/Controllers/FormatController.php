<?php

namespace App\Http\Controllers;

use App\Models\AudioFormats;
use App\Models\Format;
use App\Models\VideoFormat;
use FFMpeg\Media\Video;

class FormatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function image()
    {
        return Format::select('id', 'name')->get();
    }

    public function audio()
    {
        return AudioFormats::select('id', 'name')->get();
    }

    public function video()
    {
        return VideoFormat::select('id', 'name')->get();
    }
}
