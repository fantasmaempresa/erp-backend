<?php

/*
 * CODE
 * Salary Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @access  public
 *
 * @version 1.0
 */
class Salary extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'payment_periodicity',
            'type_tax_regime',
            'square',
            'social_security_number',
            'worker_cable',
            'worker_bank',
            'start_date',
            'job',
            'contract_type',
            'day_type',
            'job_risk',
            'base_salary',
            'integrated_daily_wage',
        ];

    /**
     * @return HasMany
     */
    public function taxData(): HasMany
    {
        return $this->hasMany(TaxDatum::class);
    }
}
