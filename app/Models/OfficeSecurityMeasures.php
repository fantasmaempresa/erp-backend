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
            'id_staff',
            'id_article',
            'adquisition_date',
            'return_date',
            'adquisition_comments',
            'return_comments',
        ];
    
    protected function setAdquisitionCommentsAttribute($value){
        $this->attributes['adquisition_comments'] = strtolower($value);
    }

    protected function getAdquisitionCommentsAttribute($value){
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
            'id_staff' => 'required|int',
            'id_article' => 'required|int',
            'adquisition_date' => 'required|date',
            'return_date' => 'required|date',
            'adquisition_comments' => 'nullable|string',
            'return_comments' => 'nullable|string',
        ];
        return $rule;
    }
}
