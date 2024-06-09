<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrantorLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grantor_links', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('father_last_name')->nullable();
            $table->string('mother_last_name')->nullable();
            $table->string('rfc')->nullable()->unique();
            $table->string('address')->nullable();

            $table->foreignId('grantor_id')->constrained();
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
        Schema::dropIfExists('grantor_links');
    }
}
