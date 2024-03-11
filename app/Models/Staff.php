<?php

/*
 * CODE
 * Staff Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class Staff extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'name',
            'last_name',
            'mother_last_name',
            'email',
            'phone',
            'nickname',
            'extra_information',
            'work_area_id',
            'user_id',
        ];

        protected function setNameAttribute($value){
            $this->attributes['name'] = strtolower($value);
        }
        
        protected function getNameAttribute($value){
            return strtoupper($value);
        }
    
        protected function setLastNameAttribute($value){
            $this->attributes['last_name'] = strtolower($value);
        }
    
        protected function getLastNameAttribute($value){
            return strtoupper($value);
        }
    
        protected function setMotherLastNameAttribute($value){
            $this->attributes['mother_last_name'] = strtolower($value);
        }
        
        protected function getMotherLastNameAttribute($value){
            return strtoupper($value);
        }
    
        protected function setEmailAttribute($value){
            $this->attributes['email'] = strtolower($value);
        }

        protected function setNicknameAttribute($value){
            $this->attributes['nickname'] = strtolower($value);
        }
        
        protected function getNicknameAttribute($value){
            return strtoupper($value);
        }

        protected function setExtraInformationAttribute($value){
            $this->attributes['extra_information'] = strtolower($value);
        }
        
        protected function getExtraInformationAttribute($value){
            return strtoupper($value);
        }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts
        = [
            'extra_information' => 'array',
        ];

    /**
     * Function to return array rules in method create and update
     *
     * @param $id
     *
     * @return array
     */
    public static function rules($id = null): array
    {
        $rule = [
            'name' => 'required|string',
            'last_name' => 'required|string',
            'mother_last_name' => 'required|string',
            'email' => 'required|email|unique:staff',
            'phone' => 'string|max:10|min:10|unique:staff',
            'nickname' => 'nullable|string',
            'extra_information' => 'nullable',
            'work_area_id' => 'int',
            'user_id' => 'nullable|int',
        ];

        if ($id) {
            // phpcs:ignore
            $rule['email'] = [
                'required',
                'email',
                Rule::unique('staff')->ignore($id),
            ];
            // phpcs:ignore
            $rule['phone'] = [
                'string',
                'max:10',
                'min:10',
                Rule::unique('staff')->ignore($id),
            ];
        }

        return $rule;
    }

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
    public function workArea(): BelongsTo
    {
        return $this->belongsTo(WorkArea::class);
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhere('name', 'like', "%$search%")
            ->orWhere('phone', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('nickname', 'like', "%$search%");
    }
}
