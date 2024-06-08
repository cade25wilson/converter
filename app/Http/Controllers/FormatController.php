<?php

namespace App\Http\Controllers;

use App\Models\ArchiveFormat;
use App\Models\AudioFormats;
use App\Models\EbookFormat;
use App\Models\Format;
use App\Models\SpreadsheetFormat;
use App\Models\VideoFormat;

class FormatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function format($type)
    {
        switch($type){
            case 'archive':
                return $this->archive();
            case 'audio':
                return $this->audio();
            case 'ebook':
                return $this->ebook();
            case 'image':
                return $this->image();
            case 'spreadsheet':
                return $this->spreadsheet();
            case 'video': 
                return $this->video();

            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }
    
    public function archive()
    {
        return ArchiveFormat::select('id', 'name')->get();
    }

    public function audio()
    {
        return AudioFormats::select('id', 'name')->get();
    }

    public function ebook()
    {
        return EbookFormat::select('id', 'name')->get();
    }

    public function image()
    {
        return Format::select('id', 'name')->get();
    }

    public function spreadsheet()
    {
        return SpreadsheetFormat::select('id', 'name')->get();
    }
    
    public function video()
    {
        return VideoFormat::select('id', 'name')->get();
    }
}
