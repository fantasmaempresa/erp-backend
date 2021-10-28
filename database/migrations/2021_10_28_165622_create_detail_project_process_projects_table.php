<?php

/*
 * CODE
 * DetailProjectProcessProject Class Migration
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateDetailProjectProcessProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_project_process_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_project_id')->constrained();
            $table->foreignId('process_project_id')->constrained();
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
        Schema::dropIfExists('detail_project_process_projects');
    }
}
