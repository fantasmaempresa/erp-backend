<?php

/*
 * Copyright CODE (2021)
 * Trait ApiResponse
 */

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

/**
 * @access  public
 *
 * @version 1.0
 */
trait ApiResponseTrait
{
    /**
     * @param $data
     * @param $code
     *
     * @return JsonResponse
     */
    protected function successResponse($data, $code): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * @param $message
     * @param $code
     *
     * @return JsonResponse
     */
    protected function errorResponse($message, $code): JsonResponse
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    /**
     * @param Collection $collection
     * @param int        $code
     *
     * @return JsonResponse
     */
    protected function showAll(Collection $collection, int $code = 200): JsonResponse
    {
        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection], $code);
        }

        $collection = $this->sortData($collection);
        $collection = $this->filterDataLike($collection);

        return $this->successResponse($collection->values(), $code);
    }

    /**
     * @param Model $instance
     * @param int   $code
     *
     * @return JsonResponse
     */
    protected function showOne(Model $instance, int $code = 200): JsonResponse
    {
        return $this->successResponse($instance, $code);
    }

    /**
     * @param     $message
     * @param int $code
     *
     * @return JsonResponse
     */
    protected function showMessage($message, int $code = 200): JsonResponse
    {
        return $this->successResponse(['message' => $message], $code);
    }

    /**
     * @param Collection $collection
     *
     * @return Collection
     */
    protected function filterData(Collection $collection): Collection
    {
        foreach (request()->query() as $query => $value) {
            if (isset($query, $value)) {
                $collection = $collection->where($query, $value);
            }
        }

        return $collection;
    }

    /**
     * @param Collection $collection
     *
     * @return Collection
     */
    protected function sortData(Collection $collection): Collection
    {
        if (request()->has('sort_by')) {
            $attribute = request()->sort_by;
            $collection = $collection->sortBy->{$attribute};
        }

        return $collection;
    }

    /**
     * @param Collection $collection
     *
     * @return Collection
     */
    protected function filterDataLike(Collection $collection): Collection
    {
        foreach (request()->query() as $query => $value) {
            if (isset($query, $value)) {
                $collection = $collection->filter(
                    function ($item) use ($query, $value) {
                        return false !== stristr($item[$query], $value);
                    }
                );
            }
        }

        return $collection;
    }

    /**
     * @param     $instance
     * @param int $code
     *
     * @return JsonResponse
     */
    protected function showList($instance, int $code = 200): JsonResponse
    {
        return $this->successResponse($instance, $code);
    }
}
