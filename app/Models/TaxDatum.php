<?php

/*
 * CODE
 * TaxDatum Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @access  public
 *
 * @version 1.0
 */
class TaxDatum extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'rfc',
            'curp',
            'regime_type',
            'postal_code',
            'street',
            'interior_number',
            'exterior_number',
            'suburb',
            'locality',
            'municipality',
            'country',
            'estate',
            'reference',
            'payment_datum_id',
        ];

    /**
     * @return BelongsTo
     */
    public function paymentDatum(): BelongsTo
    {
        return $this->belongsTo(PaymentDatum::class);
    }

    /**
     * @return BelongsToMany
     */
    public function perceptions(): BelongsToMany
    {
        return $this->belongsToMany(Perception::class);
    }

    /**
     * @return BelongsToMany
     */
    public function deductions(): BelongsToMany
    {
        return $this->belongsToMany(Deduction::class);
    }

    /**
     * @return HasMany
     */
    public function extraHours(): HasMany
    {
        return $this->hasMany(ExtraHour::class);
    }

    /**
     * @return HasMany
     */
    public function disabilities(): HasMany
    {
        return $this->hasMany(Disability::class);
    }
}
