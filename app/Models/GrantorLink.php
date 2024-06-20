<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrantorLink extends Model
{
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
        'no_int',
        'no_ext',
        'no_locality',
        'phone',
        'locality',
        'zipcode',
        'place_of_birth',
        'birthdate',
        'occupation',
        'economic_activity',
        'stake_id',
        'beneficiary',
        'grantor_id',
    ];

    public function grantor()
    {
        return $this->belongsTo(Grantor::class);
    }

    public static  function rules()
    {
        return [

            'name' => 'required|string|max:250',
            'grantor_id' => 'required|exists:grantors,id',
        ];
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    public function setFatherLastNameAttribute($value)
    {
        $this->attributes['father_last_name'] = strtolower($value);
    }

    public function getFatherLastNameAttribute($value)
    {
        return strtoupper($value);
    }

    public function setMotherLastNameAttribute($value)
    {
        $this->attributes['mother_last_name'] = strtolower($value);
    }

    public function getMotherLastNameAttribute($value)
    {
        return strtoupper($value);
    }

    public function setRfcAttribute($value)
    {
        $this->attributes['rfc'] = strtolower($value);
    }

    public function getRfcAttribute($value)
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


    public function scopeSearch($query, $search)
    {
        return $query
            ->orWhere('name', 'like', "%$search%")
            ->orWhere('father_last_name', 'like', "%$search%")
            ->orWhere('mother_last_name', 'like', "%$search%")
            ->orWhere('rfc', 'like', "%$search%")
            ->orWhere('address', 'like', "%$search%");
    }
}
