<?php

/*
 * CODE
 * PhasesProcess Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreatePhasesProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phases_processes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('form');
            $table->json('withFormat')->nullable();
            $table->tinyInteger('type_form');
            $table->boolean('payments')->default(\App\Models\PhasesProcess::$noPayment);
            $table->boolean('notification')->default(\App\Models\PhasesProcess::$noNotification);
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
        Schema::dropIfExists('phases_processes');
    }
}
