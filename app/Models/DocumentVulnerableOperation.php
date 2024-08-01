<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVulnerableOperation extends Model
{
    use HasFactory;

    protected $table = 'document_vulnerable_operation';
    public $timestamps = false;
    protected $fillable = ['id', 'document_id', 'operation_id', 'file'];
}
