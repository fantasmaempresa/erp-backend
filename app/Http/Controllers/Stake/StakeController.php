<?php

namespace App\Http\Controllers\Stake;

use App\Http\Controllers\ApiController;
use App\Models\Stake;
use Illuminate\Http\Request;

class StakeController extends ApiController
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
            $response = $this->showList(Stake::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(Stake::orderBy('id','desc')->paginate($paginate));
        }

        return $response;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Stake::rules());
        $grantor = Stake::create($request->all());

        return $this->showOne($grantor);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stake  $stake
     * @return \Illuminate\Http\Response
     */
    public function show(Stake $stake)
    {
        return $this->showOne($stake);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stake  $stake
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stake $stake)
    {
        $this->validate($request, Stake::rules());
        $stake->fill($request->all());
        if ($stake->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $stake->save();

        return $this->showOne($stake);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stake  $stake
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stake $stake)
    {
        $stake->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
