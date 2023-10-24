<?php

/*
 * OPEN2CODE ACQUIRERS
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @version1
 */
class CreateAcquirersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acquirers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rfc');
            $table->string('curp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acquirers');
    }
}
