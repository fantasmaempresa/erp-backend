<?php
/*
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 */
class FormStructure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'form',
        'description',
    ];

    protected function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }
    
    protected function getNameAttribute($value){
        return strtoupper($value);
    }

    protected function setDescriptionAttribute($value){
        $this->attributes['description'] = strtolower($value);
    }
    
    protected function getDescriptionAttribute($value){
        return strtoupper($value);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'form' => 'array',
    ];

    /**
     * Function to return array rules in method create and update
     *
     * @return array
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:250',
            'form' => 'required|array',
            'description' => 'required|string',
        ];
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->where('name', 'like', "%$search%");
    }
}
