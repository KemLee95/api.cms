<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("voucher_id");
            $table->unsignedBigInteger("user_id");
            $table->string("status", 256);
            
            $table->softDeletes();
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
        Schema::dropIfExists('voucher_user');
    }
}
