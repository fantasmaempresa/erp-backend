<?php

/*
 * CODE
 * Warehouse Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @access  public
 *
 * @version 1.0
 */
class Warehouse extends Model
{
    const ENABLED = 1;
    const DISABLED = 2;

    /**
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'name',
            'reason',
            'address',
            'accounting_account',
            'status',
        ];

    /**
     * @param null $id
     *
     * @return string[]
     */
    #[ArrayShape(['name'               => "array|string",
                  'reason'             => "string",
                  'address'            => "string",
                  'accounting_account' => "string",
                  'status'             => "string",
    ])] public static function rules($id = null)
    {
        $rules = [
            'name'               => 'required|string|unique:warehouses',
            'reason'             => 'string',
            'address'            => 'required|string',
            'accounting_account' => 'string',
            'status'             => 'int',
        ];

        if ($id) {
            $rules['name'] = [
                'required',
                Rule::unique('warehouses')->ignore($id),
            ];
        }

        return $rules;
    }
}