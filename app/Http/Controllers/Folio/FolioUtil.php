<?php

namespace App\Http\Controllers\Folio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Folio;

class FolioUtil extends Controller
{
    public function verifyRangeFolio(Book | Folio $Model, int $folio_min, int $folio_max)
    {
        
    }
}
