<?php

/*
 * OPEN2CODE
 * APPENDANT MODEL
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateAppendantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appendants', function (Blueprint $table) {
            $table->id();
            $table->integer('begin');
            $table->integer('end');
            $table->decimal('factor', 15, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appendants');
    }
}
