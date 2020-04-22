<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('supplier_code');
            $table->string('ngay_phat_hanh_dk')->nullable();
            $table->string('ngay_phat_hanh_dk_2')->nullable();
            $table->unsignedFloat('tien_hang_tra_shop')->nullable();
            $table->unsignedFloat('shop_hoan_sau_tt')->nullable();
            $table->unsignedSmallInteger('loai_ship')->default(0);
            //comment '1 mien phi, 2 tra truoc, 3 tra sau'
            $table->unsignedFloat('tien_ship')->nullable();
            $table->unsignedFloat('tong_thanh_toan_shop')->nullable();
            //'THANH TOÁN SHOP (TỆ) ( tiền trả cho shop, tiền ship trả cho shop )'

            $table->text('supplier_note')->nullable();
            $table->unsignedSmallInteger('status_supplier')->nullable();
            //'trạng thái ncc (28: từ chối thanh toán, 23: đã thanh toán, 29: còn kiện, 11: shop/xưởng giao, 12: kho nhận, 27: hủy, 26: thất lạc)'
            $table->string('ngay_phat_hanh_tt')->nullable();
            //'ngày phát hành thực tế'
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
        Schema::dropIfExists('suppliers');
    }
}
