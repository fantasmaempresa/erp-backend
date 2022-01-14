<?php

/*
 * CODE
 * DepartureDetail Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @access  public
 *
 * @version 1.0
 */
class DepartureDetail extends Model
{
    /***
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'quantity',
            'status',
            'item_id',
            'departure_id',
        ];
}