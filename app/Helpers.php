<?php

namespace App;

use App\Models\Balance;
use App\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Helpers
{

    public const ORDER_JUST_CREATE = 0;
    public const ORDER_DEPOSIT_DONE = 1;
    public const ORDER_BUYING_GOOD = 2;
    public const ORDER_WAIT_CONFIRM = 3;
    public const ORDER_WAIT_PAYMENT = 4;
    public const ORDER_PAYMENT_DONE = 5;
    public const ORDER_SHOP_DELIVERING = 6;
    public const ORDER_WAREHOUSE_RECEIVED = 7;
    public const ORDER_COMPLETED = 8;
    public const ORDER_LOST = 9;
    public const ORDER_CANCEL = 10;

    public const METHOD_DIRECT = 1;
    public const METHOD_ONLINE = 2;

    public const CUSTOMER_PAYMENT_TYPE_DEPOSIT = 1;
    public const CUSTOMER_PAYMENT_TYPE_ORDER = 2;

    public const CUSTOMER_BALANCE_TYPE_ADD = 1;
    public const CUSTOMER_BALANCE_TYPE_REFUND_DEPOSIT = 2;

    public const ORDER_TYPE_BUY_GOOD = 0;
    public const ORDER_TYPE_DELIVERY_GOOD = 1;

    public const DEPOSIT_PERCENT = 70;
    public const EXCHANGE_RATE = 3490;


    public static function addAmountByAdmin($amount, $customerId)
    {
        $customer = User::find($customerId);

        if (!$customer) {
            return 'Customer not existed!';
        }
//
//        if (!auth()->user()->isAdmin() && !auth()->user()->isSaleStaff()) {
//            return 'User not allow to perform this action!';
//        }
        DB::beginTransaction();
        try {
            Balance::create([
                'user_id' => $customerId,
                'amount' => (float)$amount,
                'staff_id' => auth()->user()->id,
                'method' => self::METHOD_DIRECT,
                'type' => self::CUSTOMER_BALANCE_TYPE_ADD
            ]);

            self::updateCustomerBalance($customerId);

            DB::commit();

            return null;

        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public static function updateCustomerBalance($customerId)
    {
        $countBalances = Balance::where('user_id', $customerId)->sum('amount');

        $countPayment = Payment::where('user_id', $customerId)->sum('amount');

        User::find($customerId)->update([
            'balance' => $countBalances - $countPayment
        ]);
    }


    public static function getListFieldCart()
    {
        return [
            'itemId',
            'itemName',
            'itemImage',
            'saleLocation',
            'wholesales',
            'requireMin',
            'propertiesType',
            'propertiesName',
            'propertiesImage',
            'propertiesId',
            'skuId',

            'itemPriceNDT',
            'stock',
            'quantity',

            'note',
        ];
    }


    public static function getItemImage($item)
    {
        if (isset(explode('http://img.taobaocdn.com', Arr::get($item, 'itemImage', ''))[1]))
            $itemImage = str_replace('http://img.taobaocdn.com', 'https://img.alicdn.com', Arr::get($item, 'itemImage', ''));
        elseif (isset(explode('http://img01.taobaocdn.com', Arr::get($item, 'itemImage', ''))[1]))
            $itemImage = str_replace('http://img01.taobaocdn.com', 'https://img.alicdn.com', Arr::get($item, 'itemImage', ''));
        elseif (isset(explode('http://img02.taobaocdn.com', Arr::get($item, 'itemImage', ''))[1]))
            $itemImage = str_replace('http://img02.taobaocdn.com', 'https://img.alicdn.com', Arr::get($item, 'itemImage', ''));
        elseif (isset(explode('http://img03.taobaocdn.com', Arr::get($item, 'itemImage', ''))[1]))
            $itemImage = str_replace('http://img03.taobaocdn.com', 'https://img.alicdn.com', Arr::get($item, 'itemImage', ''));
        elseif (isset(explode('http://img04.taobaocdn.com', Arr::get($item, 'itemImage', ''))[1]))
            $itemImage = str_replace('http://img04.taobaocdn.com', 'https://img.alicdn.com', Arr::get($item, 'itemImage', ''));
        else
            $itemImage = str_replace('http://', 'https://', Arr::get($item, 'itemImage', ''));

        return $itemImage;
    }

    public static function getOrderTypes()
    {
        return [
            self::ORDER_TYPE_BUY_GOOD => 'Đơn mua hàng',
            self::ORDER_TYPE_DELIVERY_GOOD => 'Đơn vận chuyển'
        ];
    }

    public static function getCustomerPaymentMethods()
    {
        return [
            self::METHOD_DIRECT => 'Thanh toán tiền mặt',
            self::METHOD_ONLINE => 'Thanh toán online'
        ];
    }

    public static function getCustomerPaymentTypes()
    {
        return [
            self::CUSTOMER_PAYMENT_TYPE_DEPOSIT => 'Đặt cọc',
            self::CUSTOMER_PAYMENT_TYPE_ORDER => 'Thanh toán cho đơn'
        ];
    }

    public static function getCustomerBalanceTypes()
    {
        return [
            self::CUSTOMER_BALANCE_TYPE_ADD => 'Nạp tiền vào ví',
            self::CUSTOMER_BALANCE_TYPE_REFUND_DEPOSIT => 'Trả lại từ đặt cọc'
        ];
    }


    public static function getOrderStatuses()
    {
        return [
            self::ORDER_JUST_CREATE => 'Mới tạo',
            self::ORDER_DEPOSIT_DONE => 'Đã đặt cọc',
            self::ORDER_BUYING_GOOD => 'Đang mua hàng',
            self::ORDER_WAIT_CONFIRM => 'Chờ xác nhận',
            self::ORDER_WAIT_PAYMENT => 'Chờ thanh toán',
            self::ORDER_PAYMENT_DONE => 'Đã thanh toán',
            self::ORDER_SHOP_DELIVERING => 'Shop/Xưởng đang giao hàng',
            self::ORDER_WAREHOUSE_RECEIVED => 'Kho đã nhận hàng',
            self::ORDER_COMPLETED => 'Hoàn thành',
            self::ORDER_LOST => 'Thất lạc',
            self::ORDER_CANCEL => 'Hủy'
        ];
    }

    public static function getOrderStatusByKey($key)
    {
        $ars = self::getOrderStatuses();

        return isset($ars[$key])? $ars[$key] : null;
    }




}