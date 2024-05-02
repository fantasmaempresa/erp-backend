<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\ApiController;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('paginate') ?? env('NUMBER_PAGINATE');

        if ($request->has('search') && $request->get('search') !== 'null') {
            $response = Unit::search($request->get('search'))
                ->orderBy('id', 'desc')->paginate($perPage);
        } else {
            $response = Unit::orderBy('id', 'desc')->paginate($perPage);
        }

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
        $this->validate($request, Unit::rules());
        $unit = Unit::create($request->all());

        return $this->showOne($unit);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        return $this->showOne($unit);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
        $this->validate($request, Unit::rules($unit->id));
        $unit->fill($request->all());

        if($unit->isClean()){
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $unit->save();
        return $this->showOne($unit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return $this->showMessage('Record deleted successfully');
    }
}
