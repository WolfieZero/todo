<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->boolean('complete')->default(false);
            $table->string('task');
            $table->integer('order')->default(0);
            $table->dateTime('due');
            // Replace `$table->timestamps();` with something more useful.
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        // Create foreign key cascade so if we delete user, all todos are
        // deleted as well.
        Schema::table('todos', function ($table) {
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todos', function ($table) {
            $table->dropForeign('todos_user_id_foreign');
        });
        Schema::drop('todos');
    }
}
