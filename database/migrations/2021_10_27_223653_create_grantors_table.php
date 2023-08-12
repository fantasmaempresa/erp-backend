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
            $table->string('type');
            $table->string('stake');
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
