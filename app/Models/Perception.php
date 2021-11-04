<?php

/*
 * CODE
 * Perception Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @access  public
 *
 * @version 1.0
 */
class Perception extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'key',
            'type',
            'concept',
            'aggravated_amount',
            'exempt_amount',
        ];

    /**
     * @return BelongsToMany
     */
    public function taxData(): BelongsToMany
    {
        return $this->belongsToMany(TaxDatum::class);
    }
}
