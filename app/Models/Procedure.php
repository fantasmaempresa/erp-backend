<?php
/*
 * OPEN 2 CODE PROCEDURE MODEL
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * version
 */
class Procedure extends Model
{
    use HasFactory;

    const IN_PROCESS = 1;

    const TRANSFER = 1;
    const CHECK = 2;
    const CASH = 3;

    const LAND = 1;
    const HOUSE_ROOM = 2;
    const LOCAL = 3;

    protected $fillable = [
        'id',
        'name', //Número de expediente
        'value_operation',
        'date_proceedings',
        'instrument',
        'date',
        'volume',
        'folio_min',
        'folio_max',
        'credit',
        'observation',
        'status',
        'way_to_pay',
        'real_estate_folio',
        'meters_land',
        'construction_meters',
        'property_type',
        'appraisal',
        'user_id',
        'place_id',
        'client_id',
        'staff_id',
    ];

    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    protected function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    protected function getValueOperationAttribute($value)
    {
        $cleanedValue = preg_replace('/[^0-9.]/', '', $value);
        return (int)$cleanedValue;
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function operations(): BelongsToMany
    {
        return $this->belongsToMany(Operation::class);
    }

    /**
     * @return BelongsTo
     */
    public function place(): BelongsTo
    {

        return $this->belongsTo(Place::class);
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {

        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo
     */
    public function staff(): BelongsTo
    {

        return $this->belongsTo(Staff::class);
    }

    /**
     * @return BelongsToMany
     */
    public function grantors(): BelongsToMany
    {
        return $this->belongsToMany(Grantor::class)->withPivot(['percentage', 'amount']);
    }

    /**
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class)->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ProcedureComment::class);
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        $columns = DB::getSchemaBuilder()->getColumnListing('procedures');
        return $query
            ->select('procedures.*')
            ->join('clients', 'procedures.client_id', '=', 'clients.id')
            ->join('grantor_procedure', 'procedures.id', '=', 'grantor_procedure.procedure_id')
            ->join('grantors', 'grantor_procedure.grantor_id', '=', 'grantors.id')
            ->orWhere('procedures.name', 'like', "%$search%")
            ->orWhere('value_operation', 'like', "%$search%")
            ->orWhere('instrument', 'like', "%$search%")
            ->orWhere('procedures.date', 'like', "%$search%")
            ->orWhere('volume', 'like', "%$search%")
            ->orWhere('folio_min', 'like', "%$search%")
            ->orWhere('credit', 'like', "%$search%")
            ->orWhereRaw('CONCAT(clients.name, " ", clients.last_name, " ", clients.mother_last_name) like ?', "%$search%")
            ->orWhereRaw('CONCAT(grantors.name, " ", grantors.father_last_name, " ", grantors.mother_last_name) like ?', "%$search%")
            ->groupBy($columns);
    }

    /**
     * @return HasMany
     */
    public function shapes(): HasMany
    {
        return $this->hasMany(Shape::class);
    }


    /**
     * @return string[]
     */
    public static function rules($id = null): array
    {
        $rules = [
            'name' => 'required|string|unique:procedures,name',
            'value_operation' => 'nullable|string|regex:/^[a-zA-Z0-9\s]+$/',
            'instrument' => 'required|string',
            'date' => 'required|date',
            'volume' => 'required|string',
            'folio_min' => 'nullable|string',
            'folio_max' => 'required|string',
            'credit' => 'nullable|string',
            'observation' => 'nullable|string',
            'grantors' => 'required|array',
            'documents' => 'required|array',
            'operations' => 'required|array',
            'appraisal' => 'nullable|string',
            'way_to_pay' => 'nullable|tinyint',
            'real_estate_folio' => 'nullable|string',
            'meters_land' => 'nullable|string',
            'construction_meters' => 'nullable|string',
            'property_type' => 'nullable|tinyint',
            'place_id' => 'required|exists:places,id',
            'client_id' => 'required|exists:clients,id',
            'staff_id' => 'required|exists:staff,id',
        ];

        if ($id) {
            $rules['name'] = ['required', Rule::unique('procedures')->ignore($id)];
        }

        return $rules;
    }
}
