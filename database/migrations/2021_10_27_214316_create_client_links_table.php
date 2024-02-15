<?php

/*
 * CODE
 * Client Links Class Migration
 */

use App\Models\ClientLink;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateClientLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_links', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('mother_last_name');
            $table->string('email')->unique();
            $table->string('phone', 10)->unique();
            $table->string('nickname')->nullable();
            $table->string('address')->nullable();
            $table->string('rfc')->nullable()->unique();
            $table->string('profession')->nullable();
            $table->string('degree')->nullable();
            $table->boolean('active')->default(ClientLink::INACTIVE);
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('client_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_links');
    }
}
