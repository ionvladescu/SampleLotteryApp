<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLotteryTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users_lotteries', function ($table) {
            $table->integer('ticket_num')->nullable()->after('lottery_id');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users_lotteries', function ($table) {
            $table->dropColumn('ticket_num');
        });
    }

}
