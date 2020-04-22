<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shop_id');


            $table->string('itemId')->nullable();
            $table->string('itemName')->nullable();
            $table->string('itemImage')->nullable();
            $table->string('itemLink')->nullable();
            $table->string('saleLocation')->nullable();
            $table->text('wholesales')->nullable();

            $table->text('propertiesType')->nullable();
            $table->text('propertiesName')->nullable();
            $table->text('propertiesId')->nullable();
            $table->string('propertiesImage')->nullable();
            $table->string('skuId')->nullable();
            $table->unsignedBigInteger('stock')->nullable();


            $table->unsignedFloat('itemPriceNDT')->default(0);
            $table->unsignedBigInteger('quantity')->default(0);

            $table->text('note')->nullable();

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
        Schema::dropIfExists('carts');
    }
}
