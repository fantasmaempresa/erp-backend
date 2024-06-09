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
        'rfc',
        'address',
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

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = strtolower($value);
    }

    public function getAddressAttribute($value)
    {
        return strtoupper($value);
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
