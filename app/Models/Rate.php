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
}
