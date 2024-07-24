<?php

namespace App\Http\Controllers\Folio;

use App\Http\Controllers\ApiController;
use App\Models\Book;
use App\Models\Folio;
use Illuminate\Http\Request;

class FolioActionController extends ApiController
{
    public function intstrumentRecommendation()
    {
        $lastInstrument = Folio::orderBy('name', 'desc')->first();

        $recomendedInstrument = [
            'name' => (int)$lastInstrument->name + 1,
        ];

        return $this->showList($recomendedInstrument);
    }

    public function foliosRecommendation(Request $request)
    {
        $rules = [
            'book_id' => 'required|int',
            'number_of_folios' => 'required|int',
        ];

        $this->validate($request, $rules);

        $book = Book::find($request->get('book_id'));
        $lastFolios = Folio::where('book_id', $request->book_id)->orderBy('folio_min', 'desc')->first();

        $lastFolioNumber = $lastFolios->folio_max;
        $folio_min = $lastFolioNumber + 1;
        $folio_max = $folio_min + $request->number_of_folios - 1;

        if ($book->folio_min >= $folio_min && $book->folio_max >= $folio_min && $book->folio_min >= $folio_max && $book->folio_max >= $folio_max) {
            return $this->showList(['folio_min' => $folio_min, 'folio_max' => $folio_max]);
        }else{
            return $this->errorResponse('The folio range is not valid', 422);
        }
    }
}
