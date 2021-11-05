<?php

/*
 * CODE
 * ExtraHour Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @access  public
 *
 * @version 1.0
 */
class ExtraHour extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'days',
            'type',
            'hours',
            'amount',
            'tax_datum_id',
        ];

    /**
     * @return BelongsTo
     */
    public function taxDatum(): BelongsTo
    {
        return $this->belongsTo(TaxDatum::class);
    }
}
