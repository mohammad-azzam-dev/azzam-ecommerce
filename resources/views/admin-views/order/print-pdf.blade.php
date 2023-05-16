<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{ translate('Invoice') }}</title>
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
</body>
</html>
