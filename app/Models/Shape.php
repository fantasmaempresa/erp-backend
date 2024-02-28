<?php

/**
 * OPEN2CODE
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Model shape
 */
class Shape extends Model
{
    use HasFactory;

    const REQUIRED_GRANTORS = 2;

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
        'description',
        'total',
        'data_form',
        'reverse',
        'template_shape_id',
        'procedure_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'data_form' => 'array',
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
     * @return BelongsToMany
     */
    public function grantors(): BelongsToMany
    {
        return $this->belongsToMany(Grantor::class)->withPivot('type');
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        $columns = DB::getSchemaBuilder()->getColumnListing('shapes');
        return $query
            ->select('shapes.*')
            ->join('procedures', 'shapes.procedure_id', '=', 'procedures.id')
            ->join('clients', 'procedures.client_id', '=', 'clients.id')
            ->join('grantor_shape', 'shapes.id', '=', 'grantor_shape.shape_id')
            ->join('grantors', 'grantor_shape.grantor_id', '=', 'grantors.id')
            ->orWhere('clients.name', 'like', "%$search%")
            ->orWhere('clients.last_name', 'like', "%$search%")
            ->orWhere('clients.mother_last_name', 'like', "%$search%")
            ->orWhere('grantors.name', 'like', "%$search%")
            ->orWhere('grantors.father_last_name', 'like', "%$search%")
            ->orWhere('grantors.mother_last_name', 'like', "%$search%")
            ->orWhere('folio', 'like', "%$search%")
            ->orWhere('scriptures', 'like', "%$search%")
            ->orWhere('property_account', 'like', "%$search%")
            ->orWhere('departure', 'like', "%$search%")
            ->orWhere('inscription', 'like', "%$search%")
            ->orWhere('sheets', 'like', "%$search%")
            ->orWhere('took', 'like', "%$search%")
            ->orWhere('book', 'like', "%$search%")
            ->groupBy($columns);
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
            'departure' => 'nullable|string',
            'inscription' => 'required|string',
            'sheets' => 'nullable|string',
            'took' => 'nullable|string',
            'book' => 'nullable|string',
            'operation_value' => 'required|string',
            'description' => 'required|string',
            'total' => 'required|string',
            'data_form' => 'required|array',
            'reverse' => 'nullable|string',
            'template_shape_id' => 'required|exists:template_shapes,id',
            'procedure_id' => 'required|exists:procedures,id',
            'alienating' => 'required|exists:grantors,id',
            'acquirer' => 'required|exists:grantors,id',
            'grantors.alienating.*.id' => 'required|exists:grantors,id',
            'grantors.acquirer.*.id' => 'required|exists:grantors,id',
        ];
    }


}
