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
            $table->string('father_last_name')->nullable();
            $table->string('mother_last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('rfc')->nullable()->unique();
            $table->string('curp')->nullable()->unique();
            $table->string('civil_status')->nullable();
            $table->string('municipality');
            $table->string('colony');
            $table->string('no_int')->nullable();
            $table->string('no_ext');
            $table->string('no_locality')->nullable(); // se cambio a estado
            $table->string('phone')->nullable();
            $table->string('locality');
            $table->string('zipcode');
            $table->string('place_of_birth')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('occupation')->nullable();
            $table->string('type'); //tipo de persona
            $table->string('economic_activity')->nullable();
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
