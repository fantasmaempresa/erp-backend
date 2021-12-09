<?php

/*
 * CODE
 * TaxDatum Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateTaxDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_data', function (Blueprint $table) {
            $table->id();
            $table->string('rfc', 13)->unique();
            $table->string('curp', 18)->unique();
            $table->integer('regime_type');
            $table->string('postal_code');
            $table->string('street');
            $table->string('interior_number')->nullable();
            $table->string('exterior_number');
            $table->string('suburb');
            $table->string('locality');
            $table->string('municipality');
            $table->string('country');
            $table->string('estate');
            $table->string('reference')->nullable();
            $table->foreignId('payment_datum_id')->constrained();
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
        Schema::dropIfExists('tax_data');
    }
}
