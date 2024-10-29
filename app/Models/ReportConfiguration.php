<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportConfiguration extends Model
{
    // public $timestamps = false;
    protected $fillable = [
        'id',
        'data',
        'name_process',
        'name_phase',
        'project_id',
        'process_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
