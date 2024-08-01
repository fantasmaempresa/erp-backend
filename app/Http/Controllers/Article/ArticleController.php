<?php
/**
 * @author open2code
 */
namespace App\Http\Controllers\Article;

use App\Http\Controllers\ApiController;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Place controller first version
 */
class ArticleController extends ApiController
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
            $response = $this->showList(Article::search($request->get('search'))->orderBy('id','desc')->paginate($paginate));
        } else {
            $response = $this->showList(Article::orderBy('id','desc')->paginate($paginate));
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
        $this->validate($request, Article::rules(id: null,type: $request->get('type')));
        $article = Article::create($request->all());
        $purchaseCost = $request->input('purchase_cost', 0);
        $purchaseCost = $request->input('sale_cost', 0);

        return $this->showOne($article);
    }

    /**
     * Display the specified resource.
     *
     * @param Article $article
     *
     * @return JsonResponse
     */
    public function show(Article $article): JsonResponse
    {
        return $this->showOne($article);
    }

    /**
     * @param Request $request
     * @param Article $article
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Article $article): JsonResponse
    {
        $this->validate($request, Article::rules(id: null,type: $request->get('type')));
        $article->fill($request->all());
        if ($article->isClean()) {
            return $this->errorResponse('A different value must be specified to update', 422);
        }

        $article->save();

        return $this->showOne($article);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Article $article
     *
     * @return JsonResponse
     */
    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return $this->showMessage('Record deleted successfully');
    }
}
