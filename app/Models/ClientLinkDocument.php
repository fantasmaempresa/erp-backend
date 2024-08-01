<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ClientLinkDocument extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'client_link_id',
        'document_id',
        'file',
    ];

    protected $table = "client_link_document";

    /**
    * @return BelongsTo
    */
   public function clientLink(): BelongsTo
   {
       return $this->belongsTo(ClientLink::class);
   }

   /**
    * @return BelongsTo
    */
   public function document(): BelongsTo
   {
       return $this->belongsTo(Document::class);
   }
}
