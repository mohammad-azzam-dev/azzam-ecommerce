<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\CustomerAddress;
use App\Model\DeliveryCompany;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Services\TwilioService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function list(Request $request, $status)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Order::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            if (session()->has('branch_filter') == false) {
                session()->put('branch_filter', 0);
            }
            Order::where(['checked' => 0])->update(['checked' => 1]);
            if (session('branch_filter') == 0) {
                if ($status != 'all') {
                    $query = Order::with(['customer', 'branch'])->where(['order_status' => $status]);
                } else {
                    $query = Order::with(['customer', 'branch']);
                }
            } else {
                if ($status != 'all') {
                    $query = Order::with(['customer', 'branch'])->where(['order_status' => $status, 'branch_id' => session('branch_filter')]);
                } else {
                    $query = Order::with(['customer', 'branch'])->where(['branch_id' => session('branch_filter')]);
                }
            }
        }

        $orders = $query->where('order_type', '!=', 'pos')->orderByDesc('id')->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.order.list', compact('orders', 'status', 'search'));
    }

    public function details($id)
    {
        $order = Order::with('details')->where(['id' => $id])->first();
        if (isset($order)) {
            return view('admin-views.order.order-view', compact('order'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }

    public function status(Request $request)
    {
        $order = Order::find($request->id);
        if ($request->order_status == 'out_for_delivery' && $order['delivery_man_id'] == null && $order['order_type'] != 'self_pickup') {
            Toastr::warning(translate('Please assign delivery man first!'));
            return back();
        }
        if ($request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled') {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = Product::find($detail['product_id']);
                    $type = json_decode($detail['variation'])[0]->type;
                    $var_store = [];
                    foreach (json_decode($product['variations'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['stock'] += $detail['quantity'];
                        }
                        array_push($var_store, $var);
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variations' => json_encode($var_store),
                        'total_stock' => $product['total_stock'] + $detail['quantity'],
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 0
                    ]);
                }
            }
        } else {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 0) {
                    $product = Product::find($detail['product_id']);

                    //check stock
                    foreach ($order->details as $c) {
                        $product = Product::find($c['product_id']);
                        $type = json_decode($c['variation'])[0]->type;
                        foreach (json_decode($product['variations'], true) as $var) {
                            if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                                Toastr::error(translate('Stock is insufficient!'));
                                return back();
                            }
                        }
                    }

                    $type = json_decode($detail['variation'])[0]->type;
                    $var_store = [];
                    foreach (json_decode($product['variations'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['stock'] -= $detail['quantity'];
                        }
                        array_push($var_store, $var);
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variations' => json_encode($var_store),
                        'total_stock' => $product['total_stock'] - $detail['quantity'],
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 1
                    ]);
                }
            }
        }

        $order->order_status = $request->order_status;
        $order->save();

        $fcm_token = isset($order->customer) ? $order->customer->cm_firebase_token : null;
        $value = Helpers::order_status_update_message($request->order_status);
        try {
            if ($value) {
                $data = [
                    'title' => \App\CentralLogics\translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                if($fcm_token != null) {
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(\App\CentralLogics\translate('Push notification failed for Customer!'));
        }

        //delivery man notification
        if ($request->order_status == 'processing' && $order->delivery_man != null) {
            $fcm_token = $order->delivery_man->fcm_token;
            $value = \App\CentralLogics\translate('One of your order is in processing');
            try {
                if ($value) {
                    $data = [
                        'title' => \App\CentralLogics\translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (\Exception $e) {
                Toastr::warning(\App\CentralLogics\translate('Push notification failed for DeliveryMan!'));
            }
        }

        Toastr::success(translate('Order status updated!'));
        return back();
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::find($order_id);
        if($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
            return response()->json(['status' => false], 200);
        }
        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $fcm_token = $order->delivery_man->fcm_token;
        $customer_fcm_token = isset($order->customer) ? $order->customer->cm_firebase_token : null;
        $value = Helpers::order_status_update_message('del_assign');
        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
                $cs_notify_message = Helpers::order_status_update_message('customer_notify_message');
                if($cs_notify_message) {
                    $data['description'] = $cs_notify_message;
                    if($customer_fcm_token != null) {
                        Helpers::send_push_notif_to_device($customer_fcm_token, $data);
                    }
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(\App\CentralLogics\translate('Push notification failed for DeliveryMan!'));
        }

        return response()->json(['status' => true], 200);
    }

    public function payment_status(Request $request)
    {
        $order = Order::find($request->id);
        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning('Add your payment reference code first!');
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    public function update_shipping(Request $request, $id)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    public function generate_invoice($id)
    {
        $order = Order::where('id', $id)->first();
        return view('admin-views.order.invoice-2', compact('order'));
    }

    public function add_payment_ref_code(Request $request, $id)
    {
        Order::where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success(translate('Payment reference code is added!'));
        return back();
    }

    public function branch_filter($id)
    {
        session()->put('branch_filter', $id);
        return back();
    }

    public function assign_delivery_company($order_id, $delivery_company_id)
    {
        if ($delivery_company_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::find($order_id);
        if($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
            return response()->json(['status' => false], 200);
        }
        $order->delivery_company_id = $delivery_company_id;
        $order->save();

        return response()->json(['status' => true], 200);
    }

    public function send_delivery_company_whatsapp_msg(Order $order, DeliveryCompany $company)
    {
        $twilioService = new TwilioService();

        $productNames = [];
        $productDetails = $order->details()->pluck('product_details')->toArray();
        foreach ($productDetails as $productDetail) {
            $productDetail = json_decode($productDetail);
            $productNames[] = $productDetail->name;
        }

        $productNames = implode(', ', $productNames);

        $deliveryAddress = CustomerAddress::find($order->delivery_address_id);
        $addressDetails = $deliveryAddress ? $deliveryAddress->address : '';
        $addressType = $deliveryAddress ? $deliveryAddress->address_type : '';

        // google maps link
        $link = "https://www.google.com/maps/search/?api=1&query=".$deliveryAddress->latitude.",".$deliveryAddress->longitude;


        $fullName = $order->customer->f_name . ' ' . $order->customer->l_name;
        $companyMessage = self::generateWhatsappMessage(
            $order->id,
            $fullName,
            $order->customer->phone,
            $order->order_amount,
            $productNames,
            $addressType,
            $addressDetails,
            $link
        );

        $twilioService->sendWhatsAppMessage($company->phone_number, $companyMessage);
    }

    public static function generateWhatsappMessage($orderId, $fullName, $phoneNumber, $total, $productsNames, $addressType, $addressDetails, $coordsLink): string
    {
        $total = Helpers::set_symbol($total);

        $message = "New order received:\n\n";
        $message .= "Order #$orderId\n";
        $message .= "From: $fullName\n";
        $message .= "Phone Number: $phoneNumber\n";
        $message .= "Total Amount: $total\n";
        $message .= "Product(s): $productsNames\n";
        $message .= "Address Type: $addressType\n";
        $message .= "Address: $addressDetails\n";
        $message .= "Google Maps Link: $coordsLink\n\n";
        $message .= "Thank you\n";
        $message .= "Have a good day!\n";

        return $message;
    }

    public function test($id)
    {
        $order = Order::where('id', $id)->first();
        return view('admin-views.order.print-invoice-pdf', compact('order'));
    }

}
