<?php

/*
 * CODE
 * Sales Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class Sales extends Model
{
    /***
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'date',
            'discount',
            'folio',
            'series',
            'status',
            'total',
            'subtotal',
            'warehouse_id',
            'client_id',
            'staff_id',
        ];

    /***
     * @param null $id
     *
     * @return string[]
     */
    public static function rules($id = null): array
    {
        $rules = [
            'date'         => 'required|date',
            'folio'        => 'required|string|unique:departures',
            'series'       => 'required|string',
            'status'       => 'required|integer',
            'warehouse_id' => 'required|integer',
            'user_id'      => 'required|integer',
            'discount'     => 'required|number',
            'total'        => 'required|number',
            'subtotal'     => 'required|number',
            'client_id'    => 'required|integer',
            'staff_id'     => 'required|integer',
        ];

        if ($id) {
            $rules['folio'] = [
                'required',
                Rule::unique('departures')->ignore($id),
            ];
        }

        return $rules;
    }
}
