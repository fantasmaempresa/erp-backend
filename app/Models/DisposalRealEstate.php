<?php

/*
 * OPEN2CODE DisposalRealEstate Model
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * @return BelongsTo
     */
    public function nationalConsumerPriceIndexDisposal(): BelongsTo
    {
        return $this->belongsTo(NationalConsumerPriceIndex::class, 'ncpi_disposal_id');
    }

    /**
     * @return BelongsTo
     */
    public function nationalConsumerPriceIndexAcquisition(): BelongsTo
    {
        return $this->belongsTo(NationalConsumerPriceIndex::class, 'ncpi_acquisition_id');
    }

    /**
     * @return BelongsTo
     */
    public function alienating(): BelongsTo
    {
        return $this->belongsTo(Grantor::class, 'alienating_id');
    }

    /**
     * @return BelongsTo
     */
    public function typeDisposalOperation(): BelongsTo
    {
        return $this->belongsTo(TypeDisposalOperation::class);
    }

    /**
     * @return BelongsTo
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    /**
     * @return BelongsToMany
     */
    public function acquirers(): BelongsToMany
    {
        return $this->belongsToMany(
            Grantor::class,
            'acquirer_disposal_real_estates',
            'disposal_real_estate_id',
            'acquirer_id'
        )
            ->withPivot(['proportion', 'operation_mount_value', 'tax_base', 'rate', 'isr_acquisition']);
    }

    /**
     * @param $month
     * @param $year
     *
     * @return mixed
     */
    public static function getNPCI($month, $year): mixed
    {
        $npci = NationalConsumerPriceIndex::where('month', $month)->where('year', $year)->first();
        if ($npci->value == 0) {
            $newMonth = $month - 1;
            $newYear = $year;

            if ($newMonth == 0) {
                $newMonth = 12;
                $newYear = $newYear - 1;
            }

            return self::getNPCI($newMonth, $newYear);
        } else {
            return $npci;
        }
    }

    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'alienating_id' => 'required|int',
            'type_disposal_operation_id' => 'required|int',
            'disposal_value' => 'required|numeric',
            'disposal_date' => 'required|date',
            'acquisition_value' => 'required|numeric',
            'acquisition_date' => 'required|date',
            'real_estate_appraisal' => 'required|numeric',
            'fiscal_appraisal' => 'required|numeric',
            'land_proportion' => 'required|int',
            'construction_proportion' => 'int',
            'depreciation_rate' => 'required|int',
            'improvements' => 'numeric',
            'appraisal' => 'numeric',
            'commissions' => 'numeric',
            'isabi' => 'numeric',
            'rate' => 'required|int',
            'acquirers' => 'required|array',
            'acquirers.*.id' => 'required|int',
            'acquirers.*.proportion' => 'required|int',
            'acquirers.*.rate' => 'required|int',
        ];
    }

}
