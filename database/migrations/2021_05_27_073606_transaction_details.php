<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransactionDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_id');
            $table->string('txn_id',15);
            $table->enum('type',['debit','credit']);
            $table->integer('from_user_id')->nullable();
            $table->string('from_account',15)->nullable();
            $table->enum('is_from_bank',['y','n'])->default('n');
            $table->enum('is_from_CC',['y','n'])->default('n');
            $table->enum('is_from_wallet',['y','n'])->default('n');
            $table->enum('is_from_upi',['y','n'])->default('n');
            $table->enum('is_tranfer',['y','n'])->default('n');
            $table->string('from_bank',50)->nullable();
            $table->string('trns_txn_id',15)->nullable();
            $table->string('txn_details',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
