<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClearRequestIdRelationColumnToPaidTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paid_transactions', function(Blueprint $table) {
            $table->integer('clear_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paid_transactions', function(Blueprint $table) {
            $table->dropColumn('clear_request_id');
        });
    }
}
