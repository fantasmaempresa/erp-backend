<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Procedure extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'proceedings',
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
        'extra_information',
        'operation_id',
        'user_id',
        'staff_id',
        'shape_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'extra_information' => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * @return BelongsTo
     */
    public function shape(): BelongsTo
    {
        return $this->belongsTo(Shape::class);
    }

    /**
     * @return BelongsTo
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }
}
