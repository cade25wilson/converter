<?php

namespace App\Http\Controllers;

use App\Models\ArchiveFormat;
use App\Models\AudioFormats;
use App\Models\Format;
use App\Models\SpreadsheetFormat;
use App\Models\VideoFormat;

class FormatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function image()
    {
        return Format::select('id', 'name')->get();
    }

    public function archive()
    {
        return ArchiveFormat::select('id', 'name')->get();
    }

    public function audio()
    {
        return AudioFormats::select('id', 'name')->get();
    }

    public function video()
    {
        return VideoFormat::select('id', 'name')->get();
    }

    public function spreadsheet()
    {
        return SpreadsheetFormat::select('id', 'name')->get();
    }
}
