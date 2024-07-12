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
        'operation_id',
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
        return $this->belongsToMany(Grantor::class)->withPivot(['type','principal']);
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
            ->leftJoin('grantor_shape', 'shapes.id', '=', 'grantor_shape.shape_id')
            ->leftJoin('grantors', 'grantor_shape.grantor_id', '=', 'grantors.id')
            ->orWhereRaw('CONCAT(clients.name, " ", clients.last_name, " ", clients.mother_last_name) like ?', "%$search%")
            ->orWhereRaw('CONCAT(grantors.name, " ", grantors.father_last_name, " ", grantors.mother_last_name) like ?', "%$search%")
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

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * @return string[]
     */
    public static function rules(): array
    {

        return [
            'folio' => 'required|string',
            'notary' => 'required|string',
            'scriptures' => 'nullable|string',
            'property_account' => 'required|string',
            'signature_date' => 'required|date',
            'departure' => 'nullable|string',
            'inscription' => 'nullable|string',
            'sheets' => 'nullable|string',
            'took' => 'nullable|string',
            'book' => 'nullable|string',
            'operation_value' => 'nullable|string',
            'description' => 'nullable|string',
            'total' => 'required|string',
            'data_form' => 'required|array',
            'reverse' => 'nullable|string',
            'template_shape_id' => 'required|exists:template_shapes,id',
            'procedure_id' => 'required|exists:procedures,id',
            'operation_id' => 'required|exists:operations,id',
            'alienating' => 'required|exists:grantors,id',
            'acquirer' => 'nullable|exists:grantors,id',
            'grantors.alienating.*.id' => 'required|exists:grantors,id',
            'grantors.acquirer.*.id' => 'required|exists:grantors,id',
        ];
    }
}
