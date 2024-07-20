<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeSecurityMeasures extends Model
{
    use HasFactory;

    protected $fillable
        = [
            'id',
            'staff_id',
            'article_id',
            'acquisition_date',
            'return_date',
            'acquisition_comments',
            'return_comments',
        ];
    
    protected function setAcquisitionCommentsAttribute($value){
        $this->attributes['acquisition_comments'] = strtolower($value);
    }

    protected function getAcquisitionCommentsAttribute($value){
        return strtoupper($value);
    }

    protected function setReturnCommentsAttribute($value){
        $this->attributes['return_comments'] = strtolower($value);
    }

    protected function getReturnCommentsAttribute($value){
        return strtoupper($value);
    }

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
            'staff_id' => 'required|int',
            'article_id' => 'required|int',
            'acquisition_date' => 'required|date',
            'return_date' => 'nullable|date',
            'acquisition_comments' => 'nullable|string',
            'return_comments' => 'nullable|string',
        ];
        return $rule;
    }
}
