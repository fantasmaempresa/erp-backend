<?php

/*
 * Open2Code
 * Rate Model Class
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @access  public
 *
 * @version 1.0
 */
class Rate extends Model
{
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'year',
        'lower_limit',
        'upper_limit',
        'fixed_fee',
        'surplus',
    ];

    public static function rules(){
        return [
            'year' => 'required',
            'lower_limit' => 'required',
            'upper_limit' => 'required',
            'fixed_fee' => 'required',
            'surplus' => 'required',
        ];
    }
}
