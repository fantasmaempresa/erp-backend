<?php

/*
 * CODE
 * Clients Model Class
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

/**
 * @access  public
 *
 * @version 1.0
 */
class Client extends Model
{
    use HasFactory;

    const MORAL_PERSON = 1;
    const PHYSICAL_PERSON = 2;

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
            'address',
            'rfc',
            'type',
            'profession',
            'degree',
            'extra_information',
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

    protected function setAddressAttribute($value){
        $this->attributes['address'] = strtolower($value);
    }
    
    protected function getAddressAttribute($value){
        return strtoupper($value);
    }

    protected function setRfcAttribute($value){
        $this->attributes['rfc'] = strtolower($value);
    }
    
    protected function getRfcAttribute($value){
        return strtoupper($value);
    }

    protected function setProfessionAttribute($value){
        $this->attributes['profession'] = strtolower($value);
    }
    
    protected function getProfessionAttribute($value){
        return strtoupper($value);
    }

    protected function setDegreeAttribute($value){
        $this->attributes['degree'] = strtolower($value);
    }
    
    protected function getDegreeAttribute($value){
        return strtoupper($value);
    }
    
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
    public static function rules($id = null, $type): array
    {
        $rule = [
            'name' => 'required|string',
            'last_name' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'mother_last_name' => [Rule::requiredIf($type == self::PHYSICAL_PERSON)],
            'email' => 'required|email|unique:clients',
            'phone' => 'required|string|max:10|min:10|unique:clients',
            'nickname' => 'nullable|string',
            'address' => 'nullable|string',
            'profession' => 'nullable|string',
            'degree' => 'nullable|string',
            'rfc' => 'nullable|required|string|max:13|min:10|unique:clients',
            'extra_information' => 'nullable|array',
            'user_id' => 'nullable|int',
            'type' => 'required|int',
        ];

        if ($id) {
            // phpcs:ignore
            $rule['email'] = [
                'required',
                'email',
                Rule::unique('clients')->ignore($id),
            ];
            // phpcs:ignore
            $rule['phone'] = [
                'string',
                'max:10',
                'min:10',
                Rule::unique('clients')->ignore($id),
            ];
            // phpcs:ignore
            $rule['rfc'] = [
                'string',
                'max:13',
                'min:10',
                Rule::unique('clients')->ignore($id),
            ];
        }

        return $rule;
    }

    /**
     * @return belongsTo
     */
    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function project(): HasMany
    {
        return $this->HasMany(Project::class);
    }

    /**
     * @return BelongsToMany
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }

    /**
     * @param $query
     * @param $search
     *
     * @return mixed
     */
    public function scopeSearch($query, $search): mixed
    {
        return $query->orWhereRaw('CONCAT(name, " ", last_name, " ", mother_last_name) like ?', "%$search%")
            ->orWhere('phone', 'like', "%$search%")
            ->orWhere('nickname', 'like', "%$search%")
            ->orWhere('address', 'like', "%$search%")
            ->orWhere('rfc', 'like', "%$search%");
    }
}
