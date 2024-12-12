<?php

use App\Models\Reminder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('message');
            $table->json('config');
            $table->tinyInteger('status')->default(Reminder::$NO_NOTIFED);
            $table->date('expiration_date');
            $table->tinyInteger('type'); //id de algun registro de tabla si es necesario
            $table->foreignId('user_id')->constrained(); //quien creó el registro
            $table->bigInteger('relation_id')->nullable();
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
        Schema::dropIfExists('reminders');
    }
}
