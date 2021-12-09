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
use Illuminate\Validation\Rule;

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
     * @param null $id
     *
     * @return string[]
     */
    public static function rules($id = null): array
    {
        $rules = [
            'rfc'              => 'required|string|min:13|max:13',
            'curp'             => 'required|string|min:18|max:18',
            'regime_type'      => 'required|int',
            'postal_code'      => 'required|string',
            'street'           => 'required|string',
            'interior_number'  => 'string',
            'exterior_number'  => 'required|string',
            'suburb'           => 'required|string',
            'locality'         => 'required|string',
            'municipality'     => 'required|string',
            'country'          => 'required|string',
            'estate'           => 'required|string',
            'reference'        => 'string',
            'payment_datum_id' => 'required|int',
        ];

        if ($id) {
            // phpcs:ignore
            $rules['rfc'] = [
                'required',
                'string',
                'min:13',
                'max:13',
                Rule::unique('tax_data')->ignore($id),
            ];
            // phpcs:ignore
            $rules['curp'] = [
                'required',
                'string',
                'min:18',
                'max:18',
                Rule::unique('tax_data')->ignore($id),
            ];
        }

        return $rules;
    }

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
