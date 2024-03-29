<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{ translate('Invoice') }}</title>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
</head>

<body class="footer-offset">

<main id="content" role="main" class="main pointer-event">
    <div class="content container-fluid">
        <div class="row">
            <div class="col-6">

            </div>
            <div class="col-6">
                <h2 class="float-right">{{ translate('#INVOICE') }}</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <img width="150"
                     src="{{asset('storage/app/public/ecommerce')}}/{{\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value}}">
                <br><br>
                <strong>{{ translate('Phone') }} : {{\App\Model\BusinessSetting::where(['key'=>'phone'])->first()->value}}</strong><br>
                <strong>{{ translate('Email') }} : {{\App\Model\BusinessSetting::where(['key'=>'email_address'])->first()->value}}</strong><br>
                <strong>{{ translate('Address') }} : {{\App\Model\BusinessSetting::where(['key'=>'address'])->first()->value}}</strong><br><br>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
                @if($order->customer)
                    <strong class="float-right">{{ translate('Order ID') }} : {{$order['id']}}</strong><br>
                    <strong class="float-right">{{ translate('Customer Name') }}
                        : {{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong><br>
                    <strong class="float-right">{{ translate('Phone') }}
                        : {{$order->customer['phone']}}</strong><br>
                    <strong class="float-right">{{ translate('Delivery Address') }}
                        : {{$order->delivery_address?$order->delivery_address['address']:''}}</strong><br>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header" style="display: block!important;">
                        <div class="row">
                            <div class="col-12 pb-2 border-bottom">
                                <h4 class="card-header-title">
                                    {{ translate('Order details') }}
                                    <span
                                        class="badge badge-soft-dark rounded-circle ml-1">{{$order->details->count()}}</span>
                                </h4>
                            </div>
                            <div class="col-6 pt-2">
                                <h6 style="color: #8a8a8a;">
                                    {{ translate('Order Note') }} : {{$order['order_note']}}
                                </h6>
                            </div>
                            <div class="col-6 pt-2">
                                <div class="text-right">
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">
                                        {{ translate('Payment Method') }} : {{str_replace('_',' ',$order['payment_method'])}}
                                    </h6>
                                    <h6 class="" style="color: #8a8a8a;">
                                        @if($order['transaction_reference']==null)
                                            {{ translate('Reference Code') }} :
                                            <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                                    data-target=".bd-example-modal-sm">
                                                {{ translate('Add') }}
                                            </button>
                                        @else
                                            {{ translate('Reference Code') }} : {{$order['transaction_reference']}}
                                        @endif
                                    </h6>
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">{{ translate('Order Type') }}
                                        : {{str_replace('_',' ',$order['order_type'])}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                    @php($sub_total=0)
                    @php($total_tax=0)
                    @php($total_dis_on_pro=0)
                    @php($add_ons_cost=0)
                    @foreach($order->details as $detail)
                        @if($detail->product)
                            <!-- Media -->
                                <div class="media">
                                    <div class="avatar avatar-xl mr-3">
                                        <img class="img-fluid"
                                             src="{{asset('storage/app/public/product')}}/{{json_decode($detail->product['image'],true)[0]}}"
                                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                                             alt="Image Description">
                                    </div>

                                    <div class="media-body">
                                        <div class="row">
                                            <div class="col-md-5 mb-3 mb-md-0">
                                                <strong> {{$detail->product['name']}}</strong><br>

                                                @if(count(json_decode($detail['variation'],true))>0)
                                                    <strong><u>Variation : </u></strong>
                                                    @foreach(json_decode($detail['variation'],true)[0] ?? json_decode($detail['variation'],true) as $key1 =>$variation)
                                                        <div class="font-size-sm text-body">
                                                            <span>{{$key1}} :  </span>
                                                            <span class="font-weight-bold">{{$variation}}</span>
                                                        </div>
                                                    @endforeach
                                                @endif

                                                @if( $detail->addons()->exists() )
                                                    @foreach($detail->addons as $key2 => $addon)
                                                        @if($key2==0)<strong><u>Addons : </u></strong>@endif
                                                            <div class="font-size-sm text-body">
                                                                <span>{{ $addon['addon_name'] }} :  </span>
                                                                <span class="font-weight-bold">
                                                                    {{ $addon['quantity'] }} x {{ $addon['price'] }} {{\App\CentralLogics\Helpers::currency_symbol()}}
                                                                </span>
                                                            </div>
                                                        @php($add_ons_cost += $addon['price'] * $addon['quantity'])
                                                    @endforeach
                                                @endif
                                            </div>

                                            <div class="col col-md-2 align-self-center">
                                                @if($detail['discount_on_product']!=0)
                                                    <h5>
                                                        <strike>
{{--                                                            {{ \App\CentralLogics\Helpers::set_symbol(\App\CentralLogics\Helpers::variation_price(json_decode($detail['product_details'],true)[0] ,$detail['variation'])) }}--}}
                                                        </strike>
                                                    </h5>
                                                @endif
                                                <h6>{{ Helpers::set_symbol($detail['price']-$detail['discount_on_product']) }}</h6>
                                            </div>
                                            <div class="col col-md-2 align-self-center">
                                                <h5>{{$detail['quantity']}} {{\App\CentralLogics\translate(''.$detail['unit'])}}</h5>
                                            </div>

                                            <div class="col col-md-3 align-self-center text-right">
                                                @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                                                <h5>{{ Helpers::set_symbol($amount) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @php($sub_total+=$amount)
                            @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
                            <!-- End Media -->
                                <hr>
                            @endif
                        @endforeach

                        <div class="row justify-content-md-end mb-3">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-sm-right">
                                    <dt class="col-sm-6">{{ translate('Items Price') }}:</dt>
                                    <dd class="col-sm-6">{{ Helpers::set_symbol($sub_total) }}</dd>
                                    <dt class="col-sm-6">{{ translate('Tax / VAT') }}:</dt>
                                    <dd class="col-sm-6">{{ Helpers::set_symbol($total_tax) }}</dd>
                                    <dt class="col-6">{{ translate('Addons Cost') }}:</dt>
                                    <dd class="col-6">
                                        {{Helpers::set_symbol($add_ons_cost) }}
                                        <hr>
                                    </dd>

                                    <dt class="col-sm-6">{{ translate('Subtotal') }}:</dt>
                                    <dd class="col-6">
                                        {{Helpers::set_symbol($sub_total+$total_tax+$add_ons_cost) }}</dd>
                                    <dt class="col-sm-6">{{ translate('Coupon Discount') }}:</dt>
                                    <dd class="col-sm-6">
                                        - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>
                                    <dt class="col-6">{{\App\CentralLogics\translate('Extra Discount')}}:</dt>
                                    <dd class="col-6">
                                        - {{ Helpers::set_symbol($order['extra_discount']) }}</dd>
                                    <dt class="col-sm-6">{{ translate('Delivery Fee') }}:</dt>
                                    <dd class="col-sm-6">
                                        @if($order['order_type']=='self_pickup')
                                            @php($del_c=0)
                                        @else
                                            @php($del_c=$order['delivery_charge'])
                                        @endif
                                        {{ Helpers::set_symbol($del_c) }}
                                        <hr>
                                    </dd>

                                    <dt class="col-sm-6">{{ translate('Total') }}:</dt>
                                    <dd class="col-sm-6">{{ Helpers::set_symbol($sub_total+$del_c+$total_tax+$add_ons_cost-$order['coupon_discount_amount']-$order['extra_discount']) }}</dd>
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="row justify-content-between align-items-center">
            <div class="col">
                <p class="font-size-sm mb-0">
                    &copy; {{\App\Model\BusinessSetting::where(['key'=>'restaurant_name'])->first()->value}}. <span
                        class="d-none d-sm-inline-block">{{\App\Model\BusinessSetting::where(['key'=>'footer_text'])->first()->value}}</span>
                </p>
            </div>
        </div>
    </div>
</main>

<script src="{{asset('public/assets/admin')}}/js/demo.js"></script>
<!-- JS Implementing Plugins -->
<!-- JS Front -->
<script src="{{asset('public/assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
<script>
    window.print();
</script>
</body>
</html>
