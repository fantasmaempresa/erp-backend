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
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * version
 */
class Procedure extends Model
{
    use HasFactory;

    const NOT_ASSIGNED = 'not assigned';
    const IN_PROCESS = 1;
    const NO_ACCEPTED = 2;
    const ACCEPTED = 3;

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
        'date',
        'credit',
        'observation',
        'status',
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
        return $cleanedValue;
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
        return $this->belongsToMany(Grantor::class)->withPivot(['percentage', 'amount', 'stake_id'])->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class)->withTimestamps()->withPivot(['id', 'file']);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ProcedureComment::class);
    }

    /**
     * @return HasMany
     */
    public function registrationProcedureData()
    {
        return $this->hasMany(RegistrationProcedureData::class);
    }

    /**
     * @return HasMany
     */
    public function processingIncome()
    {
        return $this->hasMany(ProcessingIncome::class);
    }

    /**
     * @return HasOne
     */
    public function folio()
    {
        return $this->hasOne(Folio::class)->with('book');
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
            ->leftJoin('clients', 'procedures.client_id', '=', 'clients.id')
            ->leftJoin('folios', 'procedures.id', '=', 'folios.procedure_id')
            ->leftJoin('books', 'folios.book_id', '=', 'books.id')
            ->leftJoin('grantor_procedure', 'procedures.id', '=', 'grantor_procedure.procedure_id')
            ->leftJoin('grantors', 'grantor_procedure.grantor_id', '=', 'grantors.id')
            ->orWhere('procedures.name', 'like', "%$search%")
            ->orWhere('value_operation', 'like', "%$search%")
            ->orWhere('procedures.date', 'like', "%$search%")
            ->orWhere('books.name', 'like', "%$search%")
            ->orWhere('folios.name', 'like', "%$search%")
            ->orWhere('folios.folio_min', '=', $search)
            ->orWhere('folios.folio_max', '=', $search)
            ->orWhereRaw('CONCAT(clients.name, " ", clients.last_name, " ", clients.mother_last_name) like ?', "%$search%")
            ->orWhereRaw('CONCAT(grantors.name, " ", grantors.father_last_name, " ", grantors.mother_last_name) like ?', "%$search%")
            ->groupBy($columns);
    }

    public function scopeAdvanceFilter($query, $filters)
    {
        if (!empty($filters->grantor_id)) {
            $query->whereHas('grantors', function ($query) use ($filters) {
                $query->whereIn('grantors.id', array_column($filters->grantor_id, 'id'));
            });
        }

        if (!empty($filters->client_id)) {
            $query->whereIn('client_id', array_column($filters->client_id, 'id'));
        }

        if (!empty($filters->user_id)) {
            $query->whereIn('user_id', array_column($filters->user_id, 'id'));
        }

        if (!empty($filters->book)) {
            $query->whereHas('folio', function ($query) use ($filters) {
                $query->whereIn('book_id', array_column($filters->book, 'id'));
            });
        }

        return $query;
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
            'name' => 'required|unique:procedures,name',
            'value_operation' => 'nullable|string|regex:/^[a-zA-Z0-9\s.]+$/',
            'date' => 'required|date',
            'credit' => 'nullable|string',
            'observation' => 'nullable|string',
            'grantors' => 'nullable|array',
            'grantors.*.grantor_id' =>  [
                'required_if:grantors,!=,null',
                'exists:grantors,id',
            ],
            'grantors.*.stake_id' => [
                'required_if:grantors,!=,null',
                'exists:stakes,id',
            ],
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
            'folio_id' => 'nullable|exists:folios,id',
        ];

        if ($id) {
            $rules['name'] = ['required', Rule::unique('procedures')->ignore($id)];
        }

        return $rules;
    }
}
