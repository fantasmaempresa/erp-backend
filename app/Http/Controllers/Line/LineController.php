<?php
/**
 * @author open2code
 */
namespace App\Http\Controllers\Line;

use App\Http\Controllers\ApiController;
use App\Models\Line;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Place controller first version
 */
class LineController extends ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $paginate = empty($request->get('paginate')) ? env('NUMBER_PAGINATE') : $request->get('paginate');

        if (!empty($request->get('search')) && $request->get('search') !== 'null') {
            $response = $this->showList(Line::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(Line::orderBy('id','desc')->paginate($paginate));
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, Line::rules());
        $line = Line::create($request->all());

        return $this->showOne($line);
    }

    /**
     * Display the specified resource.
     *
     * @param Line $line
     *
     * @return JsonResponse
     */
    public function show(Line $line): JsonResponse
    {
        return $this->showOne($line);
    }

    /**
     * @param Request $request
     * @param Line $line
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Line $line): JsonResponse
    {
        $this->validate($request, Line::rules());
        $line->fill($request->all());
        if ($line->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $line->save();

        return $this->showOne($line);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Line $line
     *
     * @return JsonResponse
     */
    public function destroy(Line $line): JsonResponse
    {
        $line->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
