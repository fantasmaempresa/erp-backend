<?php

/*
 * CODE
 * Salary Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @access  public
 *
 * @version 1.0
 */
class Salary extends Model
{
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
}
