<?php

/*
 * CODE
 * PaymentDatum Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreatePaymentDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_data', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_periodicity');
            $table->string('square', 45);
            $table->string('worker_clabe', 45)->nullable();
            $table->string('worker_bank', 45)->nullable();
            $table->string('job')->nullable();
            $table->integer('contract_type')->nullable();
            $table->integer('day_type')->nullable();
            $table->integer('job_risk')->nullable();
            $table->decimal('base_salary')->nullable();
            $table->string('integrated_daily_wage')->nullable();
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
        Schema::dropIfExists('payment_data');
    }
}
