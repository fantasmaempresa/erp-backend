<?php

namespace App\Http\Controllers\IsoDocument;

use App\Http\Controllers\ApiController;
use App\Models\IsoDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;


class IsoDocumentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');
        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = IsoDocument::search($request->get('search'))
                ->orderBy('id', 'DESC')
                ->get();
        }else {
            $response = IsoDocument::all()->orderBy('id', 'DESC');
        }

        $response->map(function ($item) {
            $item->url_file = url('storage/app/iso_documentation/' . $item->file);
            return $item;        
        });

        $currentPage = Paginator::resolveCurrentPage('page');
        $paginatedResponse = new LengthAwarePaginator(
            $response->forPage($currentPage, $paginate),
            $response->count(),
            $paginate,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

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
        $this->validate($request, IsoDocument::rules());
        $isoDocument = new IsoDocument($request->all());

        DB::begintransaction();

        try {
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('iso_documentation/', $fileName);
            $isoDocument->file = $fileName;
            $isoDocument->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('ocurrio un error al almacenar la información ' . $e->getMessage(), 422);
        }

        return $this->showOne($isoDocument);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\IsoDocument  $isoDocument
     * @return \Illuminate\Http\Response
     */
    public function show(IsoDocument $isoDocument): JsonResponse
    {
        return $this->showOne($isoDocument);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\IsoDocument  $isoDocument
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IsoDocument $isoDocument)
    {
        $this->validate($request, IsoDocument::rules());
        $isoDocument->fill($request->all());

        DB::begintransaction();

        try {
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('iso_documentation/', $fileName);
            $isoDocument->file = $fileName;
            $isoDocument->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('ocurrio un error al almacenar la información ' . $e->getMessage(), 422);
        }

        return $this->showOne($isoDocument);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IsoDocument  $isoDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy(IsoDocument $isoDocument)
    {
        $isoDocument->delete();
        return $this->successResponse('delete completed successfully');
    }
}
