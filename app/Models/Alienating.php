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
class Alienating extends Model
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
            'name' => 'string|required|unique:alienatings',
            'curp' => 'string|required|unique:alienatings',
            'rfc' => 'string|required|unique:alienatings',
        ];

        if ($id) {
            $rule['name'] = [
                Rule::unique('alienatings')->ignore($id),
            ];

            $rule['curp'] = [
                Rule::unique('alienatings')->ignore($id),
            ];

            $rule['rfc'] = [
                Rule::unique('alienatings')->ignore($id),
            ];
        }

        return $rule;
    }
}
