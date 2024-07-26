<?php

namespace App\Http\Controllers\Folio;

use App\Http\Controllers\ApiController;
use App\Models\Book;
use App\Models\Folio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolioActionController extends ApiController
{
    public function instrumentRecommendation()
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

        if ($this->isFolioRangeValid($folio_min, $folio_max, $book)) {
            return $this->showList(['folio_min' => $folio_min, 'folio_max' => $folio_max]);
        } else {
            return $this->errorResponse('The folio range is not valid', 422);
        }
    }

    public function cancelFolio(Folio $folio, Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $rules = [
            'folio' => 'required|int',
            'comment' => 'required|string',
        ];

        $this->validate($request, $rules);

        //CHECK IF FOLIO IS IN RANGE
        if(!($request->get('folio') >= $folio->folio_min && $request->get('folio') <= $folio->folio_max)) {
            return $this->errorResponse('The folio is not in range', 422);
        }


        $unusedFolios = (is_null($folio->unused_folios)) ? [] : $folio->unused_folios;
        //CHECK IF FOLIO IS ALREADY CANCELLED
        foreach ($unusedFolios as $unusedFolio) {
            if ($unusedFolio['folio'] == $request->get('folio')) {
                return $this->errorResponse('The folio is already cancelled', 422);
            }
        }
        
        $unusedFolios[] = [
            'folio' => $request->get('folio'),
            'date'  => Carbon::now()->format('Y-m-d'),
            'comment' => $request->comment,
            'user_id' => $user->id,
        ];

        //NEW FOLIO RANGE
        $book = $folio->book;
        $folio_min = $folio->folio_min;
        $folio_max = $folio->folio_max + 1;

        if ($this->isFolioRangeValid($folio_min, $folio_max, $book)) {
            $folio->unused_folios = $unusedFolios;
            $folio->folio_max = $folio_max;
            $folio->save();

            foreach ($unusedFolios as $key => $unusedFolio) {
                $unusedFolios[$key]['user'] = User::find($unusedFolio['user_id']);
            }

            return $this->showList($unusedFolios);
        } else {
            return $this->errorResponse('The folio range is not valid', 422);
        }
    }


    private function isFolioRangeValid($folio_min, $folio_max, $book)
    {
        return $folio_min >= $book->folio_min && $folio_min <= $book->folio_max
            && $folio_max >= $book->folio_min && $folio_max <= $book->folio_max;
    }
}
