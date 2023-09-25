<?php

/*
 * CODE
 * ProcessProject Class Migration
 */

use App\Models\Process;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @access  public
 *
 * @version 1.0
 */
class CreateProcessProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('process_id')->constrained();
            $table->tinyInteger('status')->default(Process::$NO_START);
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
        Schema::dropIfExists('process_project');
    }
}
