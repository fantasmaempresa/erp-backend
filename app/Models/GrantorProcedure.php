<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrantorProcedure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'grantor_id',
        'procedure_id',
        'stake_id',
        'percentage',
        'amount',
    ];

    protected $table = 'grantor_procedure';

    
    public function grantor()
    {
        return $this->belongsTo(Grantor::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function stake()
    {
        return $this->belongsTo(Stake::class);
    }
}
