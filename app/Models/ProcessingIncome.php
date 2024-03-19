<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingIncome extends Model
{
    protected $fillable = [
      'id',
      'name',
      'date_income',
      'config',
      'url_file',
      'procedure_id',
      'operation_id',
      'staff_id',
      'place_id',
      'user_id',  
    ];

    protected $casts = [
        'config' => 'array'
    ];

    public function procedure(){
        return $this->belongsTo(Procedure::class);
    }

    public function operation(){
        return $this->belongsTo(Operation::class);
    }

    public function staff(){
        return $this->belongsTo(Staff::class);
    }

    public function place(){
        return $this->belongsTo(Place::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function documents(){
        return $this->belongsToMany(Document::class);
    }

    public function processingIncomeComments(){
        return $this->hasMany(ProcessingIncomeComment::class);
    }

    public static function rules(){
        return [
            'name' => 'required',
            'date_income' => 'required',
            'config' => 'required',
            'url_file' => 'required',
            'procedure_id' => 'required',
            'operation_id' => 'required',
            'staff_id' => 'required',
            'place_id' => 'required'
        ];
    }
}
