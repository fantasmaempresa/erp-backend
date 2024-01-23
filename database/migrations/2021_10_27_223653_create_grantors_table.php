<?php

use App\Models\Grantor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrantorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grantors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('father_last_name');
            $table->string('mother_last_name');
            $table->string('rfc')->unique();
            $table->string('curp')->unique();
            $table->string('civil_status');
            $table->string('municipality');
            $table->string('colony');
            $table->string('no_int');
            $table->string('no_ext');
            $table->string('no_locality');
            $table->string('phone');
            $table->string('locality');
            $table->string('zipcode');
            $table->string('place_of_birth');
            $table->date('birthdate');
            $table->string('occupation');
            $table->string('type'); //tipo de persona
            $table->foreignId('stake_id')->constrained(); //participación
            $table->boolean('beneficiary')->default(Grantor::NO_BENEFICIARY);
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
        Schema::dropIfExists('grantors');
    }
}
