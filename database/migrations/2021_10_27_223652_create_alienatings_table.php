<?php
/*
 * OPEN2CODE Alienating
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @version1
 */
class CreateAlienatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alienatings', function (Blueprint $table) {
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
        Schema::dropIfExists('alienatings');
    }
}
