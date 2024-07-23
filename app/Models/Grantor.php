<?php

/**
 * open2code
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;


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
        'email',
        'rfc',
        'curp',
        'civil_status',
        'municipality',
        'colony',
        'street',
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
        'economic_activity',
        'beneficiary',
    ];

    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    protected function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    protected function setFatherLastNameAttribute($value)
    {
        $this->attributes['father_last_name'] = strtolower($value);
    }

    protected function getFatherLastNameAttribute($value)
    {
        return strtoupper($value);
    }

    protected function setMotherLastNameAttribute($value)
    {
        $this->attributes['mother_last_name'] = strtolower($value);
    }

    protected function getMotherLastNameAttribute($value)
    {
        return strtoupper($value);
    }

    protected function setRfcAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['rfc'] = $value;
        } else {
            $this->attributes['rfc'] = strtolower($value);
        }
    }

    protected function getRfcAttribute($value)
    {
        return strtoupper($value);
    }

    protected function setCurpAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['curp'] = $value;
        } else {
            $this->attributes['curp'] = strtolower($value);
        }
    }

    protected function getCurpAttribute($value)
    {
        return strtoupper($value);
    }

    protected function setCivilStatusAttribute($value)
    {
        $this->attributes['civil_status'] = strtolower($value);
    }

    protected function getCivilStatusAttribute($value)
    {
        return strtoupper($value);
    }

    protected function setMunicipalityAttribute($value)
    {
        $this->attributes['municipality'] = strtolower($value);
    }

    protected function getMunicipalityAttribute($value)
    {
        return ucfirst($value);
    }

    protected function setColonyAttribute($value)
    {
        $this->attributes['colony'] = strtolower($value);
    }

    protected function getColonyAttribute($value)
    {
        return ucfirst($value);
    }

    protected function setLocalityAttribute($value)
    {
        $this->attributes['locality'] = strtolower($value);
    }

    protected function getLocalityAttribute($value)
    {
        return ucfirst($value);
    }

    protected function setPlaceOfBirthAttribute($value)
    {
        $this->attributes['place_of_birth'] = strtolower($value);
    }

    protected function getPlaceOfBirthAttribute($value)
    {
        return ucfirst($value);
    }

    protected function setOccupationAttribute($value)
    {
        $this->attributes['occupation'] = strtolower($value);
    }

    protected function getOccupationAttribute($value)
    {
        return ucfirst($value);
    }

    protected function setStreetAttribute($value)
    {
        $this->attributes['street'] = strtolower($value);
    }

    protected function getStreetAttribute($value)
    {
        return strtoupper($value);
    }


    /**
     * @return BelongsToMany
     */
    public function procedures(): BelongsToMany
    {
        return $this->belongsToMany(Procedure::class)->withPivot(['percentage', 'amount', 'stake_id'])->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function shapes(): BelongsToMany
    {
        return $this->belongsToMany(Shape::class)->withPivot('type');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grantorLinks()
    {
        return $this->hasMany(GrantorLink::class);
    }

    public function grantorProcedure()
    {
        return $this->hasMany(GrantorProcedure::class);
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
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('rfc', 'like', "%$search%")
            ->orWhere('curp', 'like', "%$search%")
            ->orWhere('municipality', 'like', "%$search%")
            ->orWhere('beneficiary', 'like', "%$search%");
    }

    /**
     * @return string[]
     */
    public static function rules($id = null, $type): array
    {

        $rule = [
            'name' => 'required|string',
            'father_last_name' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'mother_last_name' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'email' => 'nullable|email|unique:grantors',
            'type' => 'required|int',
            'rfc' => 'nullable|string|unique:grantors',
            'curp' => 'nullable|string|unique:grantors',
            'civil_status' => 'nullable|string',
            'municipality' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'colony' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'street' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'no_int' => 'nullable|string',
            'no_ext' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'no_locality' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'phone' => 'nullable|string',
            'locality' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'zipcode' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'place_of_birth' => 'nullable|string',
            'birthdate' => 'required|date',
            'occupation' => 'nullable|string',
            'beneficiary' => 'required|boolean',
        ];

        if ($id) {
            // phpcs:ignore
            $rule['email'] = [
                'required', 'email',
                Rule::unique('grantors')->ignore($id)
            ];
            // phpcs:ignore
            $rule['rfc'] = [
                'required',
                Rule::unique('grantors')->ignore($id),
            ];
            // phpcs:ignore
            $rule['curp'] = [
                'string',
                // 'max:10',
                // 'min:10',
                Rule::unique('grantors')->ignore($id),
            ];
        }

        return $rule;
    }
}
