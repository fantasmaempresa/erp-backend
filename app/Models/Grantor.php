<?php
/**
 * open2code
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * first versión
 */
class Grantor extends Model
{
    use HasFactory;

    const MORAL_PERSON = 1;
    const PHYSICAL_PERSON = 2;

    const BENEFICIARY = true;
    const NO_BENEFICIARY = false;

    protected $fillable = [
        'id',
        'name',
        'father_last_name',
        'mother_last_name',
        'rfc',
        'curp',
        'civil_status',
        'municipality',
        'colony',
        'no_int',
        'no_ext',
        'no_locality',
        'phone',
        'locality',
        'zipcode',
        'place_of_birth',
        'birthdate',
        'occupation',
        'type',
        'stake_id',
        'beneficiary',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stake()
    {
        return $this->belongsTo(Stake::class);
    }

    /**
     * @return BelongsToMany
     */
    public function procedures(): BelongsToMany
    {
        return $this->belongsToMany(Procedure::class);
    }

    /**
     * @return BelongsToMany
     */
    public function shapes(): BelongsToMany
    {
        return $this->belongsToMany(Shape::class)->withPivot('type');
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
            ->orWhereRaw('CONCAT(name, " ", father_last_name, " ", mother_last_name) like ?', "%$search%")
            ->orWhere('rfc', 'like', "%$search%")
            ->orWhere('curp', 'like', "%$search%")
            ->orWhere('municipality', 'like', "%$search%")
            ->orWhere('beneficiary', 'like', "%$search%");
    }

    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'father_last_name' => 'nullable|string',
            'mother_last_name' => 'nullable|string',
            'type' => 'nullable|int',
            'rfc' => 'required|string',
            'curp' => 'required|string',
            'civil_status' => 'required|string',
            'municipality' => 'required|string',
            'colony' => 'required|string',
            'no_int' => 'nullable|string',
            'no_ext' => 'required|string',
            'no_locality' => 'required|string',
            'phone' => 'required|string',
            'locality' => 'required|string',
            'zipcode' => 'required|string',
            'place_of_birth' => 'required|string',
            'birthdate' => 'required|string',
            'occupation' => 'required|string',
            'stake_id' => 'required|exists:stakes,id',
            'beneficiary' => 'required|boolean',
        ];
    }
}
