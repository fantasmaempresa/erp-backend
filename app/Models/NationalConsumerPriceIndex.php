<?php

/*
 * CODE
 * NationalConsumerPriceIndex Model Class
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * v1
 */
class NationalConsumerPriceIndex extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'year',
        'month',
        'value',
    ];

    public static function rules()
    {
        return [
            'year' => 'required',
            'month' => 'required',
            'value' => 'required',
        ];
    }
}
