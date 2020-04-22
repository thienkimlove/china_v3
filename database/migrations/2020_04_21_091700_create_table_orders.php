<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_code')->nullable();
            $table->unsignedBigInteger('user_id');

            $table->unsignedSmallInteger('order_status')->default(0);
            $table->string('placed_date')->nullable();
            $table->string('paid_date')->nullable();

            //$table->unsignedFloat('fee_buy_good')->default(0);
            //$table->unsignedFloat('fee_tally')->default(0);
            $table->unsignedFloat('fee_close_wood')->default(0);
            $table->unsignedFloat('fee_transfer_china')->default(0);
            $table->unsignedFloat('fee_transfer_china_vn')->default(0);
            $table->unsignedFloat('fee_transfer_vn')->default(0);
            //$table->unsignedFloat('fee_good')->default(0);

            $table->unsignedFloat('total_weight')->nullable();

            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('receiver_city')->nullable();
            $table->string('receiver_district')->nullable();
            $table->text('receiver_address')->nullable();
            $table->text('receiver_note')->nullable();
            $table->text('other_order_details')->nullable();

            $table->unsignedFloat('exchange_rate');
            $table->unsignedSmallInteger('deposit_percent')->nullable();


            // don mua hang hay van chuyen
            $table->unsignedTinyInteger('order_type')->default(0);

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
        Schema::dropIfExists('orders');
    }
}
