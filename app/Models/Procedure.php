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

/**
 * version
 */
class Procedure extends Model
{
    use HasFactory;

    const IN_PROCESS = 1;

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
        'operation_id',
        'user_id',
        'place_id',
        'client_id',
        'staff_id',
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
    public function operation(): BelongsTo
    {

        return $this->belongsTo(Operation::class);
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
        return $this->belongsToMany(Grantor::class);
    }

    /**
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return$this->hasMany(ProcedureComment::class);
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
            ->orWhere('name', 'like', "%$search%")
            ->orWhere('value_operation', 'like', "%$search%")
            ->orWhere('date_proceedings', 'like', "%$search%")
            ->orWhere('instrument', 'like', "%$search%")
            ->orWhere('date', 'like', "%$search%")
            ->orWhere('volume', 'like', "%$search%")
            ->orWhere('folio_min', 'like', "%$search%")
            ->orWhere('credit', 'like', "%$search%");
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
    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'value_operation' => 'required|string',
            'date_proceedings' => 'required|string',
            'instrument' => 'required|string',
            'date' => 'required|date',
            'volume' => 'required|string',
            'folio_min' => 'nullable|string',
            'folio_max' => 'required|string',
            'credit' => 'nullable|string',
            'observation' => 'required|string',
            'grantors' => 'required|array',
            'documents' => 'required|array',
            'operation_id' => 'required|exists:operations,id',
//            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'client_id' => 'required|exists:clients,id',
            'staff_id' => 'required|exists:staff,id',
        ];
    }

}
