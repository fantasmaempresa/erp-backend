<?php

namespace App\Http\Controllers\Folio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Folio;

class FolioUtil extends Controller
{
    static public function verifyRangeFolio(Book | Folio $Model, int $folio_min, int $folio_max, int $model_id = null)
    {

        if ($model_id) {
            $model_aux =  $Model->findOrFail($model_id);
            if ($model_aux->folio_min == $folio_min && $model_aux->folio_max == $folio_max) {
                return true;
            }
        }

        $registeredRangeQuery = $Model
            ->where('folio_min', '=', $folio_min)
            ->orWhere('folio_max', '=', $folio_max)
            ->count();

        if ($registeredRangeQuery > 0) {
            return false;
        }

        // Check if the folio range overlaps with existing ranges
        $overlappingRangeQuery = $Model
            ->where(function ($query) use ($folio_min, $folio_max) {
                $query->whereBetween('folio_min', [$folio_min, $folio_max])
                    ->orWhereBetween('folio_max', [$folio_min, $folio_max]);
            })
            ->count();

        if ($overlappingRangeQuery > 0) {
            return false;
        }

        // No conflicts found, folio range is valid
        return true;
    }

    static function validateFolioRangeInBook($folioMin, $folioMax, $bookId)
    {
        $bookFolioRangeQuery = Book::findOrFail($bookId);
        $bookFolioMin = $bookFolioRangeQuery->folio_min;
        $bookFolioMax = $bookFolioRangeQuery->folio_max;

        if ($folioMin < $bookFolioMin || $folioMax > $bookFolioMax) {
            return false;
        }

        return true;
    }
}
