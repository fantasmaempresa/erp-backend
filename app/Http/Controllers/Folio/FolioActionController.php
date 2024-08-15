<?php

namespace App\Http\Controllers\Folio;

use App\Http\Controllers\ApiController;
use App\Models\Book;
use App\Models\Folio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

        if ($duplicates->isNotEmpty()) {
            return $this->errorResponse('Folios are duplicated', 422);
        }

        //CHECK IF FOLIOS ARE IN RANGE
        foreach ($request->get('canceled_folios') as $canceledFolio) {
            if (!($canceledFolio['folio'] >= $folio->folio_min && $canceledFolio['folio'] <= $folio->folio_max)) {
                return $this->errorResponse('One of folios is not in range', 422);
            }
        }

        $unusedFolios = $request->get('canceled_folios');
        $newFolios = 0;

        foreach ($unusedFolios as $key => $unusedFolio) {
            if (empty($unusedFolio['date'])) {
                $newFolios++;
                $unusedFolios[$key]['date'] = Carbon::now()->format('Y-m-d');
            }
        }

        //NEW FOLIO RANGE
        $book = $folio->book;
        $folio_min = $folio->folio_min;
        $folio_max = $folio->folio_max + $newFolios;

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

    public function unusedFolios(Book $book, Request $request)
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        $folios = $book->folios()->orderBy('folio_min', 'asc')->get();
        $foliosCount = $folios->count();
        $folioAux = [];

        $previousMaxFolio = 0;

        foreach ($folios as $key => $folio) {
            $folio->procedure;

            if (is_null($folio->procedure)) {
                $folio->color = 2;
            } else {
                $folio->color = 1;
            }

            if ($key === 0) {
                if ($folio->folio_min > $book->folio_min) {
                    $folioAux[] = [
                        'name' => '',
                        'folio_min' => $book->folio_min,
                        'folio_max' => $folio->folio_min - 1,
                        'color' => 3,
                    ];
                }
                $folioAux[] = $folio;
                $previousMaxFolio = $folio->folio_max;
            } elseif ($key === $foliosCount - 1) {
                if ($folio->folio_min > $previousMaxFolio + 1) {
                    $folioAux[] = [
                        'name' => '',
                        'folio_min' => $previousMaxFolio + 1,
                        'folio_max' => $folio->folio_min - 1,
                        'color' => 3,
                    ];
                }
                $folioAux[] = $folio;
                if ($folio->folio_max < $book->folio_max) {
                    $folioAux[] = [
                        'name' => '',
                        'folio_min' => $folio->folio_max + 1,
                        'folio_max' => $book->folio_max,
                        'color' => 3,
                    ];
                }
            } else {
                if ($folio->folio_min > $previousMaxFolio + 1) {
                    $folioAux[] = [
                        'name' => '',
                        'folio_min' => $previousMaxFolio + 1,
                        'folio_max' => $folio->folio_min - 1,
                        'color' => 3,
                    ];
                }
                $folioAux[] = $folio;
                $previousMaxFolio = $folio->folio_max;
            }
        }
        $book->folios = $folioAux;

        $page = $request->input('page', 1);
        $offset = ($page - 1) * $paginate;
        $itemsPaginados = array_slice($folioAux, $offset, $paginate);
        $paginador = new LengthAwarePaginator($itemsPaginados, count($folioAux), $paginate, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $this->showList($paginador);
    }

    public function unusedInstruments(Request $request)
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        $blockSize = 150;

        $folios = Folio::orderBy('name', 'desc');
        $foliosResult = [];

        $folios->chunk($blockSize, function ($folios) use (&$foliosResult) {
            foreach ($folios as $key => $folio) {
                if ($key === 0) {
                    $foliosResult[] = [
                        'id' => $folio->id,
                        'name' => $folio->name,
                        'folio_min' => $folio->folio_min,
                        'folio_max' => $folio->folio_max,
                        'procedure_id' => $folio->procedure_id,
                    ];
                } else {

                    while ($folio->name !== $previousInstrumentNumber - 1) {
                        $foliosResult[] = [
                            'id' => null,
                            'name' => $previousInstrumentNumber - 1,
                            'folio_min' => null,
                            'folio_max' => null,
                            'procedure_id' => null,
                        ];
                        $previousInstrumentNumber--;
                    }
                    $foliosResult[] = [
                        'id' => $folio->id,
                        'name' => $folio->name,
                        'folio_min' => $folio->folio_min,
                        'folio_max' => $folio->folio_max,
                        'procedure_id' => $folio->procedure_id,
                    ];
                }
                $previousInstrumentNumber = $folio->name;
            }
        });

        $page = $request->input('page', 1);
        $offset = ($page - 1) * $paginate;
        $itemsPaginados = array_slice($foliosResult, $offset, $paginate);
        $paginador = new LengthAwarePaginator($itemsPaginados, count($foliosResult), $paginate, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $this->showList($paginador);
    }

    public function foliosCount(Book $book)
    {
        $folios = $book->folios()->orderBy('folio_min', 'asc')->get();
        $foliosUsed = 0;
        $foliosWithoutProcedure = 0;
        $foliosUnusedCount = 0;


        for ($folio = $book->folio_min; $folio <= $book->folio_max; $folio++) {
            $folioCheck = $folios->where('folio_min', '<=', $folio)->where('folio_max', '>=', $folio)->first();
            if (!is_null($folioCheck)) {
                if ($folioCheck->procedure_id > 0) {
                    $foliosUsed++;
                } else {
                    $foliosWithoutProcedure++;
                }
            } else {
                $foliosUnusedCount++;
            }
        }

        return $this->showList([
            'folios_used_count' => $foliosUsed,
            'folios_without_procedure_count' => $foliosWithoutProcedure,
            'folios_unused_count' => $foliosUnusedCount,
        ]);
    }
}
