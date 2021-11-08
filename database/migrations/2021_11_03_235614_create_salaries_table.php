<?php

/*
 * CODE
 * Salary Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_periodicity');
            $table->string('type_tax_regime', 45);
            $table->string('square', 45);
            $table->string('social_security_number', 45);
            $table->string('worker_cable', 45);
            $table->string('worker_bank', 45);
            $table->date('start_date');
            $table->string('job');
            $table->integer('contract_type');
            $table->integer('day_type');
            $table->integer('job_risk');
            $table->decimal('base_salary');
            $table->string('integrated_daily_wage');
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
        Schema::dropIfExists('salaries');
    }
}
