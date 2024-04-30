<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operation extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'config',
        'category_operation_id'
    ];

    protected $casts = [
        'config' => 'array',
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

    public function categoryOperation(): BelongsTo
    {
        return $this->belongsTo(CategoryOperation::class);
    }


    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('name', 'like', "%$search%");
    }


    /**
     * @return HasMany
     */
    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }

    /**
     * @return string[]
     */
    public static function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category_operation_id' => 'exists:category_operations,id',
            'config' => 'array',
        ];
    }
}
