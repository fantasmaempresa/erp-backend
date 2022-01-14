<?php

/*
 * CODE
 * Item Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class Item extends Model
{
    /**
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'billable',
            'code',
            'description',
            'image',
            'line',
            'purchase_amount',
            'sale_amount',
            'status',
            'storable',
            'trademark',
            'unit_measure_sale',
            'unit_measure_purchase',
        ];


    /***
     * @param null $id
     *
     * @return string[]
     */
    public static function rules($id = null): array
    {
        $rules = [
            'billable'              => 'boolean',
            'code'                  => 'string|required|unique:items',
            'description'           => 'string|required',
            'image'                 => 'string',
            'line'                  => 'string',
            'purchase_amount'       => 'numeric',
            'sale_amount'           => 'numeric',
            'status'                => 'integer',
            'storable'              => 'boolean',
            'trademark'             => 'string',
            'unit_measure_sale'     => 'string',
            'unit_measure_purchase' => 'string',
        ];

        if ($id) {
            $rules['code'] = [
                'string',
                'required',
                Rule::unique('items')->ignore($id),
            ];
        }

        return $rules;
    }
}
