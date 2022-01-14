<?php

/*
 * CODE
 * Departure Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class Departure extends Model
{
    /***
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'date',
            'folio',
            'series',
            'status',
            'warehouse_id',
            'user_id',
        ];

    /***
     * @param null $id
     *
     * @return string[]
     */
    #[ArrayShape(['date'         => "string",
                  'folio'        => "array|string",
                  'series'       => "string",
                  'status'       => "string",
                  'warehouse_id' => "string",
                  'user_id'      => "string",
    ])] public static function rules($id = null): array
    {
        $rules = [
            'date'         => 'required|date',
            'folio'        => 'required|string|unique:departures',
            'series'       => 'required|string',
            'status'       => 'required|integer',
            'warehouse_id' => 'required|integer',
            'user_id'      => 'required|integer',
        ];

        if ($id) {
            $rules['folio'] = [
                'required',
                Rule::unique('departures')->ignore($id),
            ];
        }

        return $rules;
    }

    /**
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
