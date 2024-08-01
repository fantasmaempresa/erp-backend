<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposalRealEstatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disposal_real_estates', function (Blueprint $table) {
            $table->id();
            $table->decimal('disposal_value', 20, 4);
            $table->date('disposal_date');
            $table->decimal('acquisition_value', 20, 4);
            $table->date('acquisition_date');
            $table->decimal('real_estate_appraisal', 20, 4); //AVALUO INMOBILARIO
            $table->decimal('fiscal_appraisal', 20, 4);//AVALUO FISCAL
            $table->integer('land_proportion');
            $table->integer('construction_proportion')->nullable();
            //CCA
            $table->decimal('acquisition_value_transferor', 20, 4);
            $table->decimal('value_land_proportion', 20, 4);
            $table->decimal('value_construction_proportion', 20, 4);
            $table->integer('depreciation_rate');
            $table->decimal('annual_depreciation', 20, 4);
            $table->integer('years_passed');
            $table->decimal('depreciation_value', 20, 4)->default(0);
            $table->decimal('construction_value', 20, 4)->default(0);
            $table->decimal('annex_factor', 20, 4)->default(0);
            $table->decimal('updated_construction_cost', 20, 4)->default(0);
            $table->decimal('updated_land_cost', 20, 4)->default(0);
            $table->decimal('updated_total_cost_acquisition', 20, 4);
            //ISR DISPOSAL
            $table->decimal('disposal_value_transferor', 20, 4);
            $table->decimal('improvements', 20, 4)->default(0);
            $table->decimal('appraisal', 20, 4)->default(0);
            $table->decimal('commissions', 20, 4)->default(0);
            $table->decimal('isabi', 20, 4)->default(0);
            $table->decimal('preventive_notices', 20, 4);
            $table->decimal('tax_base', 20, 4);
            $table->decimal('cumulative_profit', 20, 4);
            $table->decimal('not_cumulative_profit', 20, 4);
            $table->decimal('surplus', 20, 4);
            $table->decimal('marginal_tax', 20, 4);
            $table->decimal('isr_charge', 20, 4);
            $table->decimal('isr_pay', 20, 4);
            $table->decimal('isr_federal_entity_pay', 20, 4);
            $table->decimal('taxable_gain', 20, 4);
            $table->decimal('rate', 20, 4);
            $table->decimal('isr_federal_entity', 20, 4);
            $table->decimal('isr_federation', 20, 4);

            $table->unsignedBigInteger('ncpi_disposal_id');
            $table->unsignedBigInteger('ncpi_acquisition_id');
            $table->unsignedBigInteger('alienating_id');

            $table->foreignId('type_disposal_operation_id')->constrained();
            $table->foreignId('rate_id')->constrained();
            $table->foreignId('appendant_id')->constrained();
            $table->foreign('alienating_id')->references('id')->on('grantors');
            $table->foreign('ncpi_disposal_id')->references('id')->on('national_consumer_price_indices');
            $table->foreign('ncpi_acquisition_id')->references('id')->on('national_consumer_price_indices');
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
        Schema::dropIfExists('disposal_real_estates');
    }
}
