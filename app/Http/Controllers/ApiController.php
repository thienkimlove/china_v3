<?php
/**
 * Created by PhpStorm.
 * User: tieungao
 * Date: 2020-04-12
 * Time: 14:16
 */

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Detail;
use App\Models\Order;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Helpers;


class ApiController
{


    public function deleteCartItem(Request $request)
    {
        $user = auth('api')->user();
        $cartId = $request->input('id');
        $cart = Cart::find($cartId);

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => "Sản phẩm trong giỏ với Id=".$cartId." không tồn tại"
            ], 400);
        }

        if ($cart->user_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => "Sản phẩm không thuộc giỏ hàng của bạn"
            ], 400);
        }


        try {

            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa item khỏi giỏ hàng!',
                'data' => Cart::where('user_id', $user->id)->get()
            ]);

        } catch (\Exception $exception) {

            return response()->json([
                'success' => false,
                'message' => "Có lỗi xảy ra: ".$exception->getMessage()
            ], 400);
        }

    }

    public function carts()
    {
        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => Cart::where('user_id', $user->id)->get()
        ]);

    }
    public function orders()
    {
        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => Order::with(['user', 'details'])
                ->where('user_id', $user->id)
                ->get()
        ]);

    }

    public function addCart(Request $request)
    {
        $user = auth('api')->user();
        $params = $request->all();

        $items = Arr::get($params, 'items');
        $shopId = Arr::get($params, 'shopId');

        if (!$items || !$shopId) {
            return response()->json([
                'success' => false,
                'message' => 'Empty items or shopId'
            ], 400);
        }

        $itemList = Helpers::getListFieldCart();


        DB::beginTransaction();
        try {

            $attrs = [
                'user_id' => $user->id,
                'aliwangwang' =>  Arr::get($params, 'aliwangwang', ''),
                'shopId' => $shopId,
                'shopName' => Arr::get($params, 'shopName', ''),
                'shopLink' => Arr::get($params, 'shopLink', ''),
                'website' => Arr::get($params, 'website', ''),
            ];

            $shop = Shop::firstOrCreate([
                    'user_id' => $user->id,
                    'shopId' => $shopId,
                ], $attrs
            );

            $responseProducts = [];

            foreach ($items as $item) {

                $itemId = Arr::get($item, 'itemId');
                $propertiesName = Arr::get($item, 'propertiesName');
                $itemPriceNDT = Arr::get($item, 'itemPriceNDT');
                $itemPriceNDT = $itemPriceNDT? $itemPriceNDT : 0;
                $quantity = Arr::get($item, 'quantity');
                $quantity = $quantity? (int) $quantity : 0;

                if ($itemId && $propertiesName && $itemPriceNDT > 0 && $quantity > 0) {
                    $product = [
                        'user_id' => $user->id,
                        'shop_id' => $shop->id,
                    ];
                    foreach ($itemList as $name) {
                        $product[$name] = Arr::get($item, $name);
                    }

                    $cart = Cart::where('user_id', $user->id)
                        ->where('itemId', $itemId)
                        ->where('propertiesName', $propertiesName)
                        ->first();

                    if ($cart) {
                        $cart->quantity = ($cart->quantity + $quantity);
                        $cart->save();
                    } else {
                        $cart = Cart::create($product);
                    }
                    $responseProducts[] = $cart;
                }

            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'add cart success',
                'data' => $responseProducts
            ], 200);

        } catch (\Exception $exception) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }

    }


    public function createOrder(Request $request)
    {
        $params = $request->all();
        $user = auth('api')->user();

        $txtComments = Arr::get($params,'txt_comments', []);
        $packageTally =  Arr::get($params,'package_tally');
        $packageWooden =  Arr::get($params, 'package_wooden');
        $txtQuantities = Arr::get($params, 'txt_quantities', []);
        $cartIds = Arr::get($params, 'cart_ids', []);

        if (!$cartIds) {
            return response()->json([
                'success' => false,
                'message' => 'No cart ids'
            ], 400);
        }


        $carts = Cart::whereIn('id', $cartIds)->get();

        if ($carts->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'No carts'
            ], 400);
        }

        DB::beginTransaction();
        try {

            $dateCurrent = Carbon::now()->toDateTimeString();
            $isTally = $packageTally ?? 0;
            $isCloseWood = $packageWooden ?? 0;
            $listFieldCart = Helpers::getListFieldCart();

            $order = Order::create([
                'user_id' => $user->id,
                'order_status' => Helpers::ORDER_JUST_CREATE,
                'placed_date' => $dateCurrent,
                'exchange_rate' => Helpers::EXCHANGE_RATE,
                'deposit_percent' => Helpers::DEPOSIT_PERCENT,
                'order_type' => Helpers::ORDER_TYPE_BUY_GOOD,
                'other_order_details' => $txtComments['sum'] ?? ''
            ]);



            foreach ($carts as $item) {
                $tempDetail = [];

                foreach ($listFieldCart as $field) {
                    $tempDetail[$field] = $item->{$field};
                }

                foreach ($txtQuantities as $txtQuantity) {
                    if ($txtQuantity['id'] == $item->id && $quantityVal = (int) $txtQuantity['qty']) {
                        $tempDetail['quantity'] = $quantityVal;
                    }
                }

                if (isset($txtComments['cart'])) {
                    foreach ($txtComments['cart'] as $comment) {
                        if ($comment['id'] == $item->id && $comment['note']) {
                            $tempDetail['note'] = $comment['note'];
                        }
                    }
                }

                $tempDetail['is_tally'] = $isTally;
                $tempDetail['is_close_wood'] = $isCloseWood;
                $tempDetail['order_id'] = $order->id;
                $tempDetail['shop_id'] = $item->shop_id;

                Detail::create($tempDetail);

                $item->delete();
            }


            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'create order success',
                'data' => $order
            ], 200);

        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }

    }

}