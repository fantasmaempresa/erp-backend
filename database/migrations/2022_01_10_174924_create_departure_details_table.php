<?php

/*
 * CODE
 * DepartureDetail Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateDepartureDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_details', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->tinyInteger('status');
            $table->foreignId('item_id')->constrained();
            $table->foreignId('departure_id')->constrained();
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
        Schema::dropIfExists('departure_details');
    }
}
