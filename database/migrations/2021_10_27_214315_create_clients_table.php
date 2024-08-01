<?php

/*
 * CODE
 * Clients Class Migration
 */

use App\Models\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('mother_last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone', 10)->unique();
            $table->string('nickname')->nullable();
            $table->string('address')->nullable();
            $table->string('rfc')->nullable()->unique();
            $table->string('profession')->nullable();
            $table->string('degree')->nullable();
            $table->tinyInteger('type')->default(Client::PHYSICAL_PERSON);
            $table->json('extra_information')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
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
        Schema::dropIfExists('clients');
    }
}
