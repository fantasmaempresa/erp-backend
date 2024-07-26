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
        $rules = [
            'canceled_folios' => 'required|array',
            'canceled_folios.*.folio' => 'required|int',
            'canceled_folios.*.comment' => 'required|string',
            'canceled_folios.*.user_id' => 'required|int',
            'save' => 'required|boolean',
        ];

        $this->validate($request, $rules);

        //CHECK FOLIOS ARE REPETED
        $foliosAux = collect($request->get('canceled_folios'))->pluck('folio');
        $folioCounts = $foliosAux->countBy();
        $duplicates = $folioCounts->filter(function ($count) {
            return $count > 1;
        });

        if($duplicates->isNotEmpty()) {
            return $this->errorResponse('Folios are duplicated', 422);
        }

        //CHECK IF FOLIOS ARE IN RANGE
        foreach ($request->get('canceled_folios') as $canceledFolio) {
            if (!($canceledFolio['folio'] >= $folio->folio_min && $canceledFolio['folio'] <= $folio->folio_max)) {
                return $this->errorResponse('One of folios is not in range', 422);
            }
        }

        $unusedFolios = (is_null($folio->unused_folios)) ? [] : $folio->unused_folios;

        //CHECK IF FOLIOS ARE ALREADY CANCELLED
        foreach ($request->get('canceled_folios') as $canceledFolio) {
            foreach ($unusedFolios as $unusedFolio) {
                if ($unusedFolio['folio'] == $canceledFolio['folio']) {
                    return $this->errorResponse('One of the folios is already cancelled', 422);
                }
            }
        }

        foreach ($request->get('canceled_folios') as $canceledFolio) {
            $unusedFolios[] = [
                'folio' => $canceledFolio['folio'],
                'date'  => Carbon::now()->format('Y-m-d'),
                'comment' => $canceledFolio['comment'],
                'user_id' => $canceledFolio['user_id'],
            ];
        }

        //NEW FOLIO RANGE
        $book = $folio->book;
        $folio_min = $folio->folio_min;
        $folio_max = $folio->folio_max + count($request->get('canceled_folios'));

        if ($this->isFolioRangeValid($folio_min, $folio_max, $book)) {
            $folio->unused_folios = $unusedFolios;
            $folio->folio_max = $folio_max;

            if ($request->get('save')) {
                $folio->save();
            }

            foreach ($unusedFolios as $key => $unusedFolio) {
                $unusedFolios[$key]['user'] = User::find($unusedFolio['user_id']);
            }

            $folio->unused_folios = $unusedFolios;

            return $this->showList($folio);
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
