<?php

namespace App\Http\Controllers\Folio;

use App\Http\Controllers\ApiController;
use App\Models\Folio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolioController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $query = Folio::search($request->get('search'))->with('user')->with('procedure');
        }
        if (!empty($request->get('view')) && $request->get('view') == 'folios') {
            $query = Folio::with('user')->with('procedure');
        } else {
            $query = Folio::where('procedure_id', null)->with('user')->with('procedure');
        }

        $response = $query->orderBy('name', 'desc')->paginate($paginate);


        return $this->showList($response);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Folio::rules());

        $folio = new Folio($request->all());
        $folio->user_id = Auth::user()->id;

        if (FolioUtil::verifyRangeFolio(new Folio(), $folio->folio_min, $folio->folio_max)) {
            if (FolioUtil::validateFolioRangeInBook($folio->folio_min, $folio->folio_max, $folio->book_id)) {
                $folio->save();
            } else {
                return $this->errorResponse('Los folios estan fuera de rango de este libro', 422);
            }
        } else {
            return $this->errorResponse('El rango de folio está ocupado por otro instrumento', 422);
        }

        return $this->showOne($folio);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Folio  $folio
     * @return \Illuminate\Http\Response
     */
    public function show(Folio $folio)
    {
        return $this->showOne($folio);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Folio  $folio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Folio $folio)
    {
        $this->validate($request, Folio::rules($folio->id));

        $folio->fill($request->all());

        if ($folio->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }


        $folio->user_id = Auth::user()->id;

        if (
            FolioUtil::verifyRangeFolio(new Folio(), $folio->folio_min, $folio->folio_max) &&
            FolioUtil::validateFolioRangeInBook($folio->folio_min, $folio->folio_max, $folio->book_id)
        ) {
            $folio->save();
        } else {
            return $this->errorResponse('Los folios estan fuera de rango', 422);
        }

        $folio->save();

        return $this->showOne($folio);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Folio  $folio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Folio $folio)
    {
        $folio->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
