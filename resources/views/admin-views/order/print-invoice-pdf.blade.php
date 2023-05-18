<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Invoice - {{ $order->customer->f_name . " " . $order->customer->l_name }}</title>

        <!-- Invoice styling -->
        <style>
            body {
                font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                text-align: center;
                color: #777;
            }

            body h1 {
                font-weight: 300;
                margin-bottom: 0px;
                padding-bottom: 0px;
                color: #000;
            }

            body h3 {
                font-weight: 300;
                margin-top: 10px;
                margin-bottom: 20px;
                font-style: italic;
                color: #555;
            }

            body a {
                color: #06f;
            }

            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                font-size: 16px;
                line-height: 24px;
                font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                color: #555;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
                border-collapse: collapse;
            }

            .invoice-box table td {
                padding: 5px;
                vertical-align: top;
            }

            /*.invoice-box table tr td:nth-child(2) {*/
            /*    text-align: right;*/
            /*}*/

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.top table td.title {
                font-size: 45px;
                line-height: 45px;
                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 40px;
            }

            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.item td {
                border-bottom: 1px solid #eee;
            }

            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.total td:nth-child(2) {
                border-top: 2px solid #eee;
                font-weight: bold;
            }

            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }

                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
            }

            .product-info {
                display: flex;
                align-items: center;
            }

            .product-image {
                width: 50px;
                height: 50px;
                margin-right: 10px;
            }

            .customer-info {
                text-align: end;
            }

            .row.justify-content-md-end.mb-3 {
                display: flex;
                justify-content: flex-end;
            }

            .col-md-9.col-lg-8 {
                flex: 0 0 auto;
                width: 100%;
                max-width: 100%;
            }

            dl.row.text-sm-right {
                display: grid;
                grid-template-columns: auto auto;
                gap: 1rem;
                justify-content: flex-end;
                text-align: right;
            }
        </style>
    </head>

    <body>
        <div class="invoice-box">
            <table>
                <tr class="top">
                    <td colspan="4">
                        <table>
                            <tr>
                                <td class="title">
                                    <img src="{{asset('storage/app/public/ecommerce')}}/{{\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value}}"
                                         alt="Company logo" style="width: 100%; max-width: 300px" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="4">
                        <table>
                            <tr>
                                <td>
                                    Phone : {{ \App\Model\BusinessSetting::where(['key'=>'phone'])->first()->value }}<br />
                                    Email : {{ \App\Model\BusinessSetting::where(['key'=>'email_address'])->first()->value }}<br />
                                    Address : {{ \App\Model\BusinessSetting::where(['key'=>'address'])->first()->value }}<br />
                                </td>



                                <td class="customer-info" style="text-align: right;">
                                    Invoice #<br />
                                    Order ID : {{ $order->id }}<br />
                                    Customer Name : {{ $order->customer->f_name . " " . $order->customer->l_name }}<br />
                                    Phone : {{ $order->customer->phone }}<br />
                                    Delivery Address : {{ $order->delivery_address ? $order->delivery_address['address'] : '' }}<br />
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td>Order Details</td>

                    <td></td>
                    <td></td>
                    <td></td>

                </tr>

                <tr>
                    <td>Payment Method</td>

                    <td></td>
                    <td></td>

                    <td> {{ str_replace('_', ' ', $order['payment_method']) }} </td>
                </tr>

                <tr>
                    <td>Order Type</td>

                    <td></td>
                    <td></td>

                    <td> {{ str_replace('_', ' ', $order['order_type']) }} </td>
                </tr>

                @php
                    $reference = 'No reference';
                    if( $order['transaction_reference'] ) {
                        $reference = $order['transaction_reference'];
                    }
                @endphp

                <tr>
                    <td>Reference Code</td>

                    <td></td>
                    <td></td>

                    <td> {{ $reference }} </td>
                </tr>

                <tr class="heading">
                    <td>Products</td>

                    <td>Price</td>
                    <td>Quantity</td>

                    <td class="customer-info" style="text-align: right;">Total</td>
                </tr>

                @php
                    $sub_total = 0;
                    $total_tax = 0;
                    $total_dis_on_pro = 0;
                    $add_ons_cost = 0;
                @endphp

                @foreach($order->details as $detail)
                    @if($detail->product)
                        <tr class="item">
                            <td>
                                <div class="product-info">
                                    @php
                                        $imgSource = json_decode($detail->product['image'],true)[0] ? asset('storage/app/public/product') ."/". json_decode($detail->product['image'],true)[0] : asset('public/assets/admin/img/160x160/img2.jpg');
                                    @endphp
                                    <img class="product-image"
                                         src="{{ $imgSource }}"
                                         alt="Image Description">

                                    <div>
                                        <span>
                                            <strong> {{ $detail->product['name'] }} </strong>
                                        </span>
                                        <br>
                                        <span>
                                            @if(count(json_decode($detail['variation'],true))>0)
                                                <strong><u>Variations : </u></strong>
                                                @foreach(json_decode($detail['variation'],true)[0] ?? json_decode($detail['variation'],true) as $key1 =>$variation)
                                                    <div class="font-size-sm text-body">
                                                        <span>{{$key1}} :  </span>
                                                        <span class="font-weight-bold">{{$variation}}</span>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </span>
                                        @if( $detail->addons()->exists() )
                                            @foreach($detail->addons as $key2 => $addon)
                                                @if($key2==0)<strong><u>Addons : </u></strong>@endif
                                                <div class="font-size-sm text-body">
                                                    <span>{{ $addon['addon_name'] }} :  </span>
                                                    <span class="font-weight-bold">
                                                        {{ $addon['quantity'] }} x {{ $addon['price'] }} {{\App\CentralLogics\Helpers::currency_code()}}
                                                    </span>
                                                </div>
                                                @php($add_ons_cost += $addon['price'] * $addon['quantity'])
                                            @endforeach
                                        @endif

                                    </div>

                                </div>
                            </td>

                            <td>
                                {{ Helpers::set_code($detail['price']-$detail['discount_on_product']) }}
                            </td>
                            <td>
                                {{ $detail['quantity'] }} {{ $detail['unit'] }}
                            </td>

                            @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                            <td class="customer-info" style="text-align: right;">{{ Helpers::set_code($amount) }}</td>
                        </tr>

                        @php($sub_total+=$amount)
                        @php($total_tax+=$detail['tax_amount']*$detail['quantity'])

                    @endif
                @endforeach
            </table>

            <table style="width: 100%;">
                <tr>
                    <td style="text-align: right;">
                        <span style="display: block;">Items Price: {{ Helpers::set_code($sub_total) }}</span>
                        <span style="display: block;">Tax / VAT : {{ Helpers::set_code($total_tax) }}</span>
                        <span style="display: block;">Addons Cost: {{Helpers::set_code($add_ons_cost) }}</span>
                        <hr>
                        <span style="display: block;">Subtotal: {{Helpers::set_code($sub_total+$total_tax+$add_ons_cost) }}</span>
                        <span style="display: block;">Coupon Discount: - {{ Helpers::set_code($order['coupon_discount_amount']) }}</span>
                        <span style="display: block;">Extra Discount: - {{ Helpers::set_code($order['extra_discount']) }}</span>
                        @if($order['order_type']=='self_pickup')
                            @php($del_c=0)
                        @else
                            @php($del_c=$order['delivery_charge'])
                        @endif
                        <span style="display: block;">Delivery Fee: {{ Helpers::set_code($del_c) }}</span>
                        <hr>
                        <span style="display: block;">Total: {{ Helpers::set_code($sub_total+$del_c+$total_tax+$add_ons_cost-$order['coupon_discount_amount']-$order['extra_discount']) }}</span>
                    </td>
                </tr>
            </table>

            <div class="footer" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="row">
                    <div class="col" style="flex: 1;">
                        <p class="font-size-sm mb-0" style="font-size: small; margin-bottom: 0;">
                            &copy; <span class="restaurant-name" style="color: red;"></span>{{\App\Model\BusinessSetting::where(['key'=>'restaurant_name'])->first()->value}}. <span class="footer-text" style="font-weight: bold;"></span>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
