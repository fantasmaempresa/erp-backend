<?php

/*
 * CODE
 * TaxDatum Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @access  public
 *
 * @version 1.0
 */
class TaxDatum extends Model
{
    protected $fillable
        = [
            'id',
            'rfc',
            'curp',
            'start_date_employment',
            'business_name',
            'street',
            'interior_number',
            'exterior_number',
            'suburb',
            'municipality',
            'tax_data_col',
            'county',
            'estate',
            'reference',
            'staff_id',
        ];
}