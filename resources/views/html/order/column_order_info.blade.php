@foreach ($entry->details as $detail)

<div class="tp-img">
    <a href="javascript:void(0)">
        <img src="{{ $detail->itemImage }}">
    </a>
</div>
<div class="tp-content">
    <div class="box-order-top">
        <p class="title-order text-bold"
           data-toggle="tooltip"
           title=""
           data-original-title="Mã đơn hàng">
            <a href="" class="ng-binding" data-code="{{ $detail->detail_code }}">{{ $detail->detail_code }}</a>
            <span class="label-warning label"
                  data-value="1"
                  data-text="{{ \App\Helpers::getOrderStatusByKey($entry->order_status) }}">
                {{ \App\Helpers::getOrderStatusByKey($entry->order_status) }}
            </span>
        </p>
        <span class="sticker nowrap">
            <span class="label-success label" data-value="1" data-text="Order">Order</span>
        </span>

        <div class="box-text text-hidden">
            <div class="row-gr">
                <span class="col-left">Shop:
                    <span class="col-right">{{ $detail->shop->shopName }}</span>
                </span>
            </div>
        </div>
    </div>
</div>


@endforeach
