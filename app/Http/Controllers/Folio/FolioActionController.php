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

        $folio_min = (!is_null($lastFolios)) ? $lastFolios->folio_max + 1 : $book->folio_min;
        $folio_max = $folio_min + $request->number_of_folios - 1;

        if ($folio_min >= $book->folio_min && $folio_min <=$book->folio_max && $folio_max >= $book->folio_min && $folio_max <= $book->folio_max) {
            return $this->showList(['folio_min' => $folio_min, 'folio_max' => $folio_max]);
        } else {
            return $this->errorResponse('The folio range is not valid', 422);
        }
    }

    public function cancelFolio(Folio $folio, Request $request)
    {
        $rules = [
            'folio' => 'required|int',
            'comment' => 'required|string',
        ];

        $unusedFolios = (is_null($folio->unused_folios)) ? [] : $folio->unused_folios;
        $unusedFolios[] = [
            'folio' => $request->folio,
            'comment' => $request->comment,
        ];
    }
}
