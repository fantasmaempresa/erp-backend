<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessingIncomeComment extends Model
{
    protected $fillable = [
        'id',
        'comment',
        'user_id',
        'processing_income_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function processingIncome(){
        return $this->belongsTo(ProcessingIncome::class);
    }

    public static function rules(){
        return [
            'comment' => 'required',
            'processing_income_id' => 'required',
        ];
    }
}
