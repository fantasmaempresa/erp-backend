<?php

namespace App\Http\Controllers\GrantorLink;

use App\Http\Controllers\ApiController;
use App\Models\GrantorLink;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GrantorLinkController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, ['grantor_id' => 'required']);

        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        if (!empty($request->get('search')) && $request->get('search') !== 'null'){
            $grantorLinks = GrantorLink::where('grantor_id', $request->get('grantor_id'))
                ->search($request->get('search'))
                ->with('grantor')
                ->orderBy('name')
                ->paginate($paginate);
        }else{
            $grantorLinks = GrantorLink::where('grantor_id', $request->get('grantor_id'))
                ->with('grantor')
                ->orderBy('name')
                ->paginate($paginate);
        }

        return $this->showList($grantorLinks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, GrantorLink::rules());
        $grantorLink = new GrantorLink($request->all());
        $grantorLink->birthdate = Carbon::parse($grantorLink->birthdate);
        $grantorLink->save(); 
        return $this->showOne($grantorLink, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GrantorLink  $grantorLink
     * @return \Illuminate\Http\Response
     */
    public function show(GrantorLink $grantorLink)
    {
        $grantorLink->grantor;
        return $this->showOne($grantorLink);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GrantorLink  $grantorLink
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GrantorLink $grantorLink)
    {
        $this->validate($request, GrantorLink::rules());
        $grantorLink->fill($request->all());
        if ($grantorLink->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }
        $grantorLink->birthdate = Carbon::parse($grantorLink->birthdate);
        $grantorLink->save();
        return $this->showOne($grantorLink);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GrantorLink  $grantorLink
     * @return \Illuminate\Http\Response
     */
    public function destroy(GrantorLink $grantorLink)
    {
        $grantorLink->delete();
        return $this->showMessage("Record deleted successfully");
    }
}
