<?php

namespace App\Models;

use App\Helpers;
use App\User;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'orders';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    protected $fillable = [
        'user_id',
        'order_code',
        'order_type',
        'exchange_rate',
        'deposit_percent',

        'order_status',
        'placed_date',
        'paid_date',

        //'fee_buy_good',
        'fee_tally',
        'fee_close_wood',
        'fee_transfer_china',
        'fee_transfer_china_vn',
        'fee_transfer_vn',
        //'fee_good',

        'total_weight',

        'receiver_name',
        'receiver_phone',
        'receiver_email',
        'receiver_city',
        'receiver_district',
        'receiver_address',
        'receiver_note',

        'other_order_details',
        'external_order_code',
        'external_order_amount_ndt'
    ];

    public $appends = [
        'total_amount_ndt',
        'total_amount_vnd',
        'amount_need_deposit',
        'amount_already_deposit'
    ];

    public function getAmountAlreadyDepositAttribute()
    {
        $amount = 0;
        if ($this->payments) {
            foreach ($this->payments as $payment){
                if ($payment->type == Helpers::CUSTOMER_PAYMENT_TYPE_DEPOSIT) {
                    $amount += round($payment->amount);
                }
            }
        }
        if ($this->balances) {
            foreach ($this->balances as $balance) {
                if ($balance->type == Helpers::CUSTOMER_BALANCE_TYPE_REFUND_DEPOSIT) {
                    $amount -= round($balance->amount);
                }
            }
        }
        return $amount;
    }


    public function getTotalAmountNdtAttribute()
    {
        return round($this->details->sum('total_amount_ndt'));
    }

    public function getTotalAmountVndAttribute()
    {
        return round($this->details->sum('total_amount_vnd'));
    }

    public function getAmountNeedDepositAttribute()
    {
        return round(($this->total_amount_vnd*$this->deposit_percent)/100);
    }

    public function canMakeDeposit()
    {
        return ($this->order_status == Helpers::ORDER_JUST_CREATE);
    }

    public function makeDeposit($note)
    {
        if (!$this->canMakeDeposit()) {
            return 'Không thể thực hiện hành động này!';
        }

        if ($this->amount_need_deposit > $this->user->balance) {
            return 'Tài khoản khách hàng không đủ để đặt cọc cho đơn!';
        }

        DB::beginTransaction();
        try {

            // tru tien trong vi by make payment

            Payment::create([
                'user_id' => $this->user->id,
                'amount' => $this->amount_need_deposit,
                'order_id' => $this->id,
                'type' => Helpers::CUSTOMER_PAYMENT_TYPE_DEPOSIT,
                'method' => Helpers::METHOD_DIRECT,
                'staff_id' => auth()->user()->id,
                'note' => $note
            ]);

            // cap nhat don hang.

            $this->update([
                'order_status' => Helpers::ORDER_DEPOSIT_DONE
            ]);


            //cap nhat balance customer
            Helpers::updateCustomerBalance($this->user->id);

            DB::commit();

            return null;


        } catch (\Exception $exception) {
            DB::rollBack();

            return 'Có lỗi : '.$exception->getMessage();
        }

    }


    public function canMakeBuying()
    {
        return ($this->order_status == Helpers::ORDER_DEPOSIT_DONE);
    }

    public function makeBuying($note)
    {
        if (!$this->canMakeBuying()) {
            return 'Không thể thực hiện hành động này!';
        }

        DB::beginTransaction();

        try {
            if ($this->amount_need_deposit ==  $this->amount_already_deposit) {
                $this->update([
                    'order_status' => Helpers::ORDER_BUYING_GOOD
                ]);

            } else if ($this->amount_need_deposit < $this->amount_already_deposit) {

                Balance::create([
                    'order_id' => $this->id,
                    'amount' => $this->amount_already_deposit - $this->amount_need_deposit,
                    'method' => Helpers::METHOD_DIRECT,
                    'type' => Helpers::CUSTOMER_BALANCE_TYPE_REFUND_DEPOSIT,
                    'user_id' => $this->user->id,
                    'staff_id' => auth()->user()->id,
                    'note' => $note
                ]);

                Helpers::updateCustomerBalance($this->user->id);

                $this->update([
                    'order_status' => Helpers::ORDER_BUYING_GOOD
                ]);
            } else {
                // amount need to deposit more.
                $amountNeedMore = $this->amount_need_deposit - $this->amount_already_deposit;

                if ($this->user->balance < $amountNeedMore) {
                    return 'Không đủ tiền trong ví khách hàng để đặt cộc thêm cho đơn!';
                } else {
                    Payment::create([
                        'user_id' => $this->user->id,
                        'amount' => $amountNeedMore,
                        'order_id' => $this->id,
                        'type' => Helpers::CUSTOMER_PAYMENT_TYPE_DEPOSIT,
                        'method' => Helpers::METHOD_DIRECT,
                        'staff_id' => auth()->user()->id,
                        'note' => $note
                    ]);
                    Helpers::updateCustomerBalance($this->user->id);
                    $this->update([
                        'order_status' => Helpers::ORDER_BUYING_GOOD
                    ]);
                }
            }

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();

            return 'Có lỗi : '.$exception->getMessage();
        }

    }


    public function canMakeWaitConfirm()
    {
        return ($this->order_status == Helpers::ORDER_BUYING_GOOD);
    }

    public function makeWaitConfirm($externalOrderCode)
    {
        if (!$this->canMakeWaitConfirm()) {
            return 'Không thể thực hiện hành động này!';
        }

        if (!$externalOrderCode) {
            return 'Xin nhập vào mã đơn ngoài!';
        }

        DB::beginTransaction();

        try {
            $this->update([
                'order_status' => Helpers::ORDER_WAIT_CONFIRM,
                'external_order_code' => $externalOrderCode
            ]);
            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return 'Có lỗi : '.$exception->getMessage();
        }

    }

    public function canMakeWaitPaymentFull()
    {
        return ($this->order_status == Helpers::ORDER_BUYING_GOOD);
    }

    public function makeWaitPaymentFull($externalOrderCode, $externalOrderAmountNDT)
    {
        if (!$this->canMakeWaitPaymentFull()) {
            return 'Không thể thực hiện hành động này!';
        }

        if (!$externalOrderCode) {
            return 'Xin nhập vào mã đơn ngoài!';
        }

        if (!$externalOrderAmountNDT) {
            return 'Xin nhập vào số tiền cần thanh toán (NDT)!';
        }

        DB::beginTransaction();

        try {
            $this->update([
                'order_status' => Helpers::ORDER_WAIT_PAYMENT,
                'external_order_code' => $externalOrderCode,
                'external_order_amount_ndt' => $externalOrderAmountNDT
            ]);
            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return 'Có lỗi : '.$exception->getMessage();
        }

    }

    public function canMakeWaitPaymentPart()
    {
        return ($this->order_status == Helpers::ORDER_WAIT_CONFIRM);
    }

    public function makeWaitPaymentPart($externalOrderAmountNDT)
    {
        if (!$this->canMakeWaitPaymentPart()) {
            return 'Không thể thực hiện hành động này!';
        }


        if (!$externalOrderAmountNDT) {
            return 'Xin nhập vào số tiền cần thanh toán (NDT)!';
        }

        DB::beginTransaction();

        try {
            $this->update([
                'order_status' => Helpers::ORDER_WAIT_PAYMENT,
                'external_order_amount_ndt' => $externalOrderAmountNDT
            ]);
            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return 'Có lỗi : '.$exception->getMessage();
        }

    }



    public function canMakePaymentDone()
    {
        return ($this->order_status == Helpers::ORDER_WAIT_PAYMENT);
    }

    public function makePaymentDone($accountId)
    {
        if (!$this->canMakePaymentDone()) {
            return 'Không thể thực hiện hành động này!';
        }

        if (!$accountId || !($account = Account::find($accountId))) {
            return 'Xin chọn tài khoản Alipay thanh toán!';
        }

        DB::beginTransaction();

        try {

            Amount::create([
                'order_id' => $this->id,
                'account_id' => $account->id,
                'staff_id' => auth()->user()->id,
                'amount' => $this->external_order_amount_ndt
            ]);

            $this->update([
                'order_status' => Helpers::ORDER_PAYMENT_DONE
            ]);
            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return 'Có lỗi : '.$exception->getMessage();
        }

    }



    protected $casts = [
        'placed_date' => 'datetime',
        'paid_date' => 'datetime',
    ];

    public function afterCreated()
    {
        $customerOrderCount = Order::where('user_id', $this->user_id)->count();

        $this->update([
            'order_code' => $this->user->code.'_'.($customerOrderCount+1)
        ]);

        try {
            Action::create([
                'order_id' => $this->id,
                'order_status' => $this->order_status,
                'staff_id' => auth()->user()->id,
            ]);
        } catch (\Exception $exception) {
            //exception
        }
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(Detail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }


    public function columnOrderInfo()
    {
        return '';
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
