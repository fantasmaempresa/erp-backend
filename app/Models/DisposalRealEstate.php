<?php

/*
 * OPEN2CODE DisposalRealEstate Model
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @version
 */
class DisposalRealEstate extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'disposal_value',
        'disposal_date',
        'acquisition_value',
        'acquisition_date',
        'real_estate_appraisal',
        'fiscal_appraisal',
        'land_proportion',
        'construction_proportion',
        'acquisition_value_transferor',
        'value_land_proportion',
        'value_construction_proportion',
        'depreciation_rate',
        'annual_depreciation',
        'years_passed',
        'depreciation_value',
        'construction_value',
        'annex_factor',
        'updated_construction_cost',
        'updated_land_cost',
        'disposal_value_transferor',
        'updated_total_cost_acquisition',
        'improvements',
        'appraisal',
        'commissions',
        'isabi',
        'preventive_notices',
        'tax_base',
        'cumulative_profit',
        'not_cumulative_profit',
        'surplus',
        'marginal_tax',
        'isr_charge',
        'isr_pay',
        'isr_federal_entity_pay',
        'taxable_gain',
        'rate',
        'isr_federal_entity',
        'isr_federation',
        'ncpi_disposal_id',
        'ncpi_acquisition_id',
        'alienating_id',
        'type_disposal_operation_id',
        'rate_id',
    ];

    /**
     * @return void
     */
    public static function rules()
    {

    }
}
