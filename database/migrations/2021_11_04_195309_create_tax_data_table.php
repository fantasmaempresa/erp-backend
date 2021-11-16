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
            $table->string('rfc');
            $table->string('curp');
            $table->date('start_date_employment');
            $table->string('business_name');
            $table->string('street');
            $table->string('interior_number');
            $table->string('exterior_number');
            $table->string('suburb');
            $table->string('municipality');
            $table->string('tax_data_col');
            $table->string('county');
            $table->string('estate');
            $table->string('reference');
            $table->foreignId('staff_id')->constrained();
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
