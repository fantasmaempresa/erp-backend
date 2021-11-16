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
            $table->string('employee_number');
            $table->date('start_date_employment')->nullable();
            $table->string('office', 240)->nullable();
            $table->string('social_security_number', 240)->nullable();
            $table->string('worker_clabe', 45)->nullable();
            $table->string('worker_bank', 45)->nullable();
            $table->string('job')->nullable();
            $table->integer('contract_type')->nullable();
            $table->integer('day_type')->nullable();
            $table->string('employer_registration')->nullable();
            $table->integer('job_risk')->nullable();
            $table->decimal('base_salary')->nullable();
            $table->string('integrated_daily_wage')->nullable();
            $table->foreignId('staff_id')->constrained();
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
