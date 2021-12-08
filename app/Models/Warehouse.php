<?php

/*
 * CODE
 * Warehouse Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}