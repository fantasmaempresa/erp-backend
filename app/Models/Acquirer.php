<?php

/*
 * OPEN2CODE
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * @version1
 */
class Acquirer extends Model
{
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'curp',
        'rfc',
    ];

    /**
     * @param  $id
     *
     * @return string[]
     */
    public static function rules($id = null): array
    {
        $rule = [
            'name' => 'string|required|unique:acquirers',
            'curp' => 'string|required|unique:acquirers',
            'rfc' => 'string|required|unique:acquirers',
        ];

        if ($id) {
            $rule['name'] = [
                Rule::unique('acquirers')->ignore($id),
            ];

            $rule['curp'] = [
                Rule::unique('acquirers')->ignore($id),
            ];

            $rule['rfc'] = [
                Rule::unique('acquirers')->ignore($id),
            ];
        }

        return $rule;
    }
}
