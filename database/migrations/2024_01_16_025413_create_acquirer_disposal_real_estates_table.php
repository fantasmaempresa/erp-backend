<?php

/**
 * OPEN2CODE
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @version
 */
class CreateAcquirerDisposalRealEstatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acquirer_disposal_real_estates', function (Blueprint $table) {
            $table->integer('proportion');
            $table->decimal('operation_mount_value', 20, 4);
            $table->decimal('tax_base', 20, 4);
            $table->decimal('rate', 20, 4);
            $table->decimal('isr_acquisition', 20, 4);
            $table->unsignedBigInteger('acquirer_id');
            $table->foreignId('disposal_real_estate_id')->constrained();

            $table->foreign('acquirer_id')->references('id')->on('grantors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acquirer_disposal_real_estates');
    }
}
