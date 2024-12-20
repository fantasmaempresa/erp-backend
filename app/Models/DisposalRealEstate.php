<?php

/*
 * OPEN2CODE DisposalRealEstate Model
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

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
        'appendant_id',
        'ncpi_disposal_id',
        'ncpi_acquisition_id',
        'alienating_id',
        'type_disposal_operation_id',
        'rate_id',
    ];

    public function scopeSearch($query, $search)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing('disposal_real_estates');
        $query->select('disposal_real_estates.*')
            ->join('grantors as alienating', 'disposal_real_estates.alienating_id', '=', 'alienating.id')
            ->join('acquirer_disposal_real_estates', 'disposal_real_estates.id', '=', 'acquirer_disposal_real_estates.disposal_real_estate_id')
            ->join('grantors as acquirer', 'acquirer_disposal_real_estates.acquirer_id', '=', 'acquirer.id')
            ->where(function ($query) use ($search) {
                $query->orWhereRaw('CONCAT(acquirer.name, " ", acquirer.father_last_name, " ", acquirer.mother_last_name) like ?', "%$search%")
                    ->orWhereRaw('CONCAT(alienating.name, " ", alienating.father_last_name, " ", alienating.mother_last_name) like ?', "%$search%");
            })->groupBy($columns);
    }

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
    public function rates(): BelongsTo
    {
        return $this->belongsTo(Rate::class, 'rate_id');
    }

    public function appendant(): BelongsTo
    {

        return $this->belongsTo(Appendant::class);
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
            ->withPivot(['proportion', 'operation_mount_value', 'tax_base', 'rate', 'isr_acquisition', 'fiscal_appraisal', 'disposal_value']);
    }

    /**
     * @param $month
     * @param $year
     *
     * @return mixed
     */
    public static function getNPCI($month, $year): mixed
    {
        $priceIndex = NationalConsumerPriceIndex::where('month', $month)
            ->where('year', $year)
            ->first();

        if (is_null($priceIndex) || $priceIndex->value == 0) {
            $previousMonth = $month == 1 ? 12 : $month - 1;
            $previousYear = $month == 1 ? $year - 1 : $year;

            return self::getNPCI($previousMonth, $previousYear);
        }

        return $priceIndex;
    }


    public static function getRate($disposalDateYear, $cumulative_profit)
    {
        $rate = Rate::where('year', $disposalDateYear)
            ->where('lower_limit', '<=', $cumulative_profit)
            ->where('upper_limit', '>=', $cumulative_profit)
            ->first();

        if (is_null($rate)) {
            return self::getRate($disposalDateYear - 1, $cumulative_profit);
        }

        return $rate;
    }
    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'alienating_id' => 'required|int',
            'type_disposal_operation_id' => 'required|int',
            'disposal_value' => 'required|numeric|gt:acquisition_value',
            'disposal_date' => 'required|date|after:acquisition_date',
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

    /**
     * @param $acquirers
     * @return void
     * @throws \Exception
     */
    public function calculations($acquirers)
    {
        //CCA
        $landProportion = $this->land_proportion / 100;
        $constructionProportion = isset($this->construction_proportion) ? $this->construction_proportion / 100 : 0;
        $depreciationRate = $this->depreciation_rate / 100;

        $this->acquisition_value_transferor = $this->acquisition_value / $acquirers;
        $this->value_land_proportion = $this->acquisition_value_transferor * $landProportion;
        $this->value_construction_proportion = $this->acquisition_value_transferor * $constructionProportion;
        $this->annual_depreciation = $depreciationRate * $this->value_construction_proportion;

        $disposalDate = new \DateTime($this->disposal_date);
        $acquisitionDate = new \DateTime($this->acquisition_date);
        $differenceDate = $disposalDate->diff($acquisitionDate);
        $this->years_passed = ($differenceDate->y) ? $differenceDate->y : 1;
        
        $this->depreciation_value = $this->annual_depreciation * $this->years_passed;
        $this->construction_value = $this->value_construction_proportion - $this->depreciation_value;
        
        $npciDisposal = DisposalRealEstate::getNPCI(
            (int)$disposalDate->format('m') - 1,
            (int)$disposalDate->format('Y')
        );
        $npciAcquisition = DisposalRealEstate::getNPCI(
            (int)$acquisitionDate->format('m'),
            (int)$acquisitionDate->format('Y')
        );
        
        $this->ncpi_disposal_id = $npciDisposal->id;
        $this->ncpi_acquisition_id = $npciAcquisition->id;
        $this->annex_factor = round(($npciDisposal->value / $npciAcquisition->value), 4);
        
        $appendant = Appendant::where('begin', $this->years_passed)->first();
        $this->appendant_id = $appendant->id;
        
        $this->updated_construction_cost = $this->construction_value * $this->annex_factor;
        $this->updated_land_cost = $this->acquisition_value_transferor * $this->annex_factor;
        $this->updated_total_cost_acquisition = $this->updated_land_cost + $this->updated_construction_cost;
        
        //ISR DISPOSAL
        $this->disposal_value_transferor = $this->disposal_value / $acquirers;
        $this->preventive_notices = $this->improvements + $this->appraisal + $this->commissions + $this->isabi;
        $this->tax_base = $this->disposal_value_transferor
        - $this->updated_total_cost_acquisition
        - $this->preventive_notices;

        $this->cumulative_profit = $this->tax_base / $this->years_passed;
        $this->not_cumulative_profit = $this->tax_base - $this->cumulative_profit;
        
        $rate = self::getRate($disposalDate->format('Y'), $this->cumulative_profit);
        
        $this->rate_id = $rate->id;
        $this->surplus = $this->cumulative_profit - $rate->lower_limit;
        $this->marginal_tax = $this->surplus * ($rate->surplus / 100);
        $this->isr_charge = $this->marginal_tax + $rate->fixed_fee;
        $this->isr_pay = $this->isr_charge * $this->years_passed;
        $this->isr_federal_entity_pay = $this->isr_pay;
        $this->taxable_gain = $this->tax_base;
        $this->isr_federal_entity = $this->taxable_gain * ($this->rate / 100);
        $this->isr_federation = $this->isr_federal_entity_pay - $this->isr_federal_entity;
    }
    
    public function acquirerCalcultation($aquire)
    {
        $grantor = Grantor::find($aquire['grantor_id']);
        $operationMountValue = $this->disposal_value * .10;
        $taxBase = (($this->fiscal_appraisal - $this->disposal_value) > $operationMountValue) ?
        ($this->fiscal_appraisal - $this->disposal_value) :
        0;
        $isrAcquisition = $taxBase * ($aquire['rate'] / 100);

        $this->acquirers()->attach($grantor, [
            'proportion' => $aquire['proportion'],
            'operation_mount_value' => $operationMountValue,
            'tax_base' => $taxBase,
            'rate' => $aquire['rate'],
            'isr_acquisition' => $isrAcquisition,
            'fiscal_appraisal' => $this->fiscal_appraisal,
            'disposal_value' => $this->disposal_value_transferor,
        ]);
    }
}
