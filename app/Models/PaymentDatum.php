<?php

/*
 * CODE
 * PaymentDatum Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class PaymentDatum extends Model
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
            'square',
            'employee_number',
            'start_date_employment',
            'office',
            'social_security_number',
            'worker_clabe',
            'worker_bank',
            'job',
            'contract_type',
            'day_type',
            'employer_registration',
            'job_risk',
            'base_salary',
            'integrated_daily_wage',
            'staff_id',
        ];

    /**
     * @return HasMany
     */
    public function taxData(): HasMany
    {
        return $this->hasMany(TaxDatum::class);
    }

    /**
     * @return BelongsTo
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

}
