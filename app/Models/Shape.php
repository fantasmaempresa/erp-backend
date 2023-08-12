<?php

/**
 * OPEN2CODE
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model shape
 */
class Shape extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'folio',
        'notary',
        'scriptures',
        'property_account',
        'signature_date',
        'departure',
        'inscription',
        'sheets',
        'took',
        'book',
        'operation_value',
        'alienating_name',
        'alienating_street',
        'alienating_outdoor_number',
        'alienating_interior_number',
        'alienating_colony',
        'alienating_locality',
        'alienating_municipality',
        'alienating_entity',
        'alienating_zipcode',
        'alienating_phone',
        'acquirer_name',
        'description',
        'total',
        'data_form',
        'template_shape_id',
        'procedure_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'data_form' => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function template_shape(): BelongsTo
    {
        return $this->belongsTo(TemplateShape::class);
    }

    /**
     * @return BelongsTo
     */
    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }


    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query
            ->orWhere('folio', 'like', "%$search%")
            ->orWhere('notary', 'like', "%$search%")
            ->orWhere('scriptures', 'like', "%$search%")
            ->orWhere('property_account', 'like', "%$search%")
            ->orWhere('departure', 'like', "%$search%")
            ->orWhere('inscription', 'like', "%$search%")
            ->orWhere('sheets', 'like', "%$search%")
            ->orWhere('took', 'like', "%$search%")
            ->orWhere('book', 'like', "%$search%");
    }

    /**
     * @return string[]
     */
    public static function rules(): array
    {

        return [
            'folio' => 'required|string',
            'notary' => 'required|string',
            'scriptures' => 'required|string',
            'property_account' => 'required|string',
            'signature_date' => 'required|date',
            'departure' => 'required|string',
            'inscription' => 'required|string',
            'sheets' => 'required|string',
            'took' => 'required|string',
            'book' => 'required|string',
            'operation_value' => 'required|string',
            'alienating_name' => 'required|string',
            'alienating_street' => 'required|string',
            'alienating_outdoor_number' => 'required|string',
            'alienating_interior_number' => 'required|string',
            'alienating_colony' => 'required|string',
            'alienating_locality' => 'required|string',
            'alienating_municipality' => 'required|string',
            'alienating_entity' => 'required|string',
            'alienating_zipcode' => 'required|string',
            'alienating_phone' => 'required|string',
            'acquirer_name' => 'required|string',
            'description' => 'required|string',
            'total' => 'required|string',
            'data_form' => 'required|array',
            'template_shape_id' => 'required|exists:template_shapes,id',
            'procedure_id' => 'required|exists:procedures,id',
        ];
    }


}
