<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotteriesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('lotteries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by')->nullable()->unsigned();
            $table->bigInteger('winner_id')->nullable()->unsigned();
            $table->string('title', 200)->nullable();
            $table->longText('description')->nullable();
            $table->string('image_url', 511)->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('draw_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

/*            $table->foreign('winner_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('lotteries');
    }
}
