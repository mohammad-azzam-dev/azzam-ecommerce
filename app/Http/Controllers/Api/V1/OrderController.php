<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Model\Addon;
use App\Model\Admin;
use App\Model\BusinessSetting;
use App\Model\CustomerAddress;
use App\Model\DMReview;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\OrderDetailAddon;
use App\Model\Product;
use App\Model\Review;
use App\Services\TwilioService;
use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }

    public function place_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_amount' => 'required',
            'delivery_address_id' => 'required',
            'order_type' => 'required|in:self_pickup,delivery',
            'branch_id' => 'required'
        ]);

        //check stock
        foreach ($request['cart'] as $c) {
            $product = Product::find($c['product_id']);
            if (count(json_decode($product['variations'], true)) > 0) {
                $type = $c['variation'][0]['type'];
                foreach (json_decode($product['variations'], true) as $var) {
                    if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                        $validator->getMessageBag()->add('stock', 'Stock is insufficient! available stock ' . $var['stock']);
                    }
                }
            } else {
                if ($product['total_stock'] < $c['quantity']) {
                    // $validator->getMessageBag()->add('stock', 'Stock is insufficient! available stock ' . $var['stock']);
                    $validator->getMessageBag()->add('stock', 'Stock is insufficient! available stock');
                }
            }
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            $o_id = 100000 + Order::all()->count() + 1;

            $or = [
                'id' => $o_id,
                'user_id' => $request->user()->id,
                'order_amount' => $request['order_amount'],
                'coupon_discount_amount' => $request->coupon_discount_amount,
                'coupon_discount_title' => $request->coupon_discount_title == 0 ? null : 'coupon_discount_title',
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'coupon_code' => $request['coupon_code'],
                'payment_method' => $request->payment_method,
                'transaction_reference' => null,
                'order_note' => $request['order_note'],
                'order_type' => $request['order_type'],
                'branch_id' => $request['branch_id'],
                'delivery_address_id' => $request->delivery_address_id,
                'delivery_charge' => BusinessSetting::where(['key' => 'delivery_charge'])->first()->value,
                'delivery_address' => json_encode(CustomerAddress::find($request->delivery_address_id) ?? null),
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::table('orders')->insertGetId($or);

            $productNames = [];
            foreach ($request['cart'] as $c) {
                $product = Product::find($c['product_id']);
                $productNames[] = $product->name;
                if (count(json_decode($product['variations'], true)) > 0) {
                    $price = Helpers::variation_price($product, json_encode($c['variation']));
                } else {
                    $price = $product['price'];
                }

                $or_d = [
                    'order_id' => $o_id,
                    'product_id' => $c['product_id'],
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $price,
                    'unit' => $product['unit'],
                    'tax_amount' => Helpers::tax_calculate($product, $price),
                    'discount_on_product' => Helpers::discount_calculate($product, $price),
                    'discount_type' => 'discount_on_product',
                    'variant' => json_encode($c['variant']),
                    'variation' => json_encode($c['variation']),
                    'is_stock_decreased' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if (count(json_decode($product['variations'], true)) > 0) {
                    $type = $c['variation'][0]['type'];
                    $var_store = [];
                    foreach (json_decode($product['variations'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['stock'] -= $c['quantity'];
                        }
                        array_push($var_store, $var);
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variations' => json_encode($var_store),
                        'total_stock' => $product['total_stock'] - $c['quantity']
                    ]);
                } else {
                    Product::where(['id' => $product['id']])->update([
                        'total_stock' => $product['total_stock'] - $c['quantity']
                    ]);
                }

                $orderDetailId = DB::table('order_details')->insertGetId($or_d);

                if ( isset($c['addons_details']) && count($c['addons_details']) ) {
                    $orderDetailAddons = [];
                    foreach ($c['addons_details'] as $addonDetail) {
                        $addon = Addon::find($addonDetail['id']);

                        if($addon) {
                            $orderDetailAddons[] = [
                                'order_detail_id' => $orderDetailId,
                                'addon_id' => $addon->id,
                                'addon_name' => $addon->name,
                                'price' => $addon->price,
                                'quantity' => $addonDetail['quantity'] ?? 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    OrderDetailAddon::insert($orderDetailAddons);
                }


            }

            $fcm_token = $request->user()->cm_firebase_token;
            $value = Helpers::order_status_update_message('pending');
            try {
                if ($value) {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $o_id,
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }

                //send email
//                Mail::to($request->user()->email)->send(new \App\Mail\OrderPlaced($o_id));

            } catch (\Exception $e) {
            }

            // send whatsapp message
            $twilioService = new TwilioService();

            $productNames = implode(', ', $productNames);
            $deliveryAddress = CustomerAddress::find($request->delivery_address_id);
            $addressDetails = $deliveryAddress ? $deliveryAddress->address : '';
            $addressType = $deliveryAddress ? $deliveryAddress->address_type : '';

            // google maps link
            $link = "https://www.google.com/maps/search/?api=1&query=".$deliveryAddress->latitude.",".$deliveryAddress->longitude;

            $admin = Admin::find(1);
            $adminPhoneNumber = $admin->phone;

            $fullName = $request->user()->f_name . ' ' . $request->user()->l_name;
            $adminMessage = \App\Http\Controllers\Admin\OrderController::generateWhatsappMessage(
                $o_id,
                $fullName,
                $request->user()->phone,
                $request['order_amount'],
                $productNames,
                $addressType,
                $addressDetails,
                $link
            );

            $twilioService->sendWhatsAppMessage($adminPhoneNumber, $adminMessage);

//            self::sendInvoicePdfToAdminAndUser($o_id);

            return response()->json([
                'message' => 'Order placed successfully!',
                'order_id' => $o_id
            ], 200);

            /*Mail::to($email)->send(new \App\Mail\OrderPlaced($o_id));*/
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 403);
        }
    }

    public function get_order_list(Request $request)
    {
        $orders = Order::with(['customer', 'delivery_man.rating'])
            ->withCount('details')
            ->where(['user_id' => $request->user()->id])->get();

        $orders->map(function ($data) {
            $data['deliveryman_review_count'] = DMReview::where(['delivery_man_id' => $data['delivery_man_id'], 'order_id' => $data['id']])->count();
            return $data;
        });

        return response()->json($orders->map(function ($data) {
            $data->details_count = (integer)$data->details_count;
            return $data;
        }), 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = OrderDetail::where(['order_id' => $request['order_id']])->get();

        if ($details->count() > 0) {
            foreach ($details as $det) {
                $det['variation'] = json_decode($det['variation'], true);
                if (Order::find($request->order_id)->order_type == 'pos') {
                    $det['variation'] = (string)implode('-', array_values($det['variation'])) ?? null;
                }
                else {
                    if(count($det['variation'])>0 && $det['variation'][0]['type'] == null && $det['variation'][0]['price'] == null && $det['variation'][0]['stock'] == null) {
                        $det['variation'] = null;
                    } else {
                        $det['variation'] = (string) (isset($det['variation'][0]) ? ($det['variation'][0]['type'] . '-' . $det['variation'][0]['price'] . '-' . $det['variation'][0]['stock']) : null);
                    }
                }

                $det['review_count'] = Review::where(['order_id' => $det['order_id'], 'product_id' => $det['product_id']])->count();
                $product = Product::where('id', $det['product_id'])->first();

                if(!isset($product)) {
                    $details[0]->product_details = json_decode($details[0]->product_details);

                    $details[0]->product_details->category_ids = gettype($details[0]->product_details->category_ids) != 'array' ? json_decode($details[0]->product_details->category_ids) : $details[0]->product_details->category_ids;
                    $details[0]->product_details->image = gettype($details[0]->product_details->category_ids) != 'array' ? json_decode($details[0]->product_details->image) : $details[0]->product_details->image;
                }
                $det['product_details'] = isset($product) ? Helpers::product_data_formatting($product) : $details[0]->product_details;
            }
            return response()->json($details, 200);
        } else {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => 'not found!']
                ]
            ], 401);
        }
    }

    public function cancel_order(Request $request)
    {
        if (Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->first()) {

            $order = Order::with(['details'])->where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->first();

            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = Product::find($detail['product_id']);
                    if (count(json_decode($product['variations'], true)) > 0) {
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
                    } else {
                        Product::where(['id' => $product['id']])->update([
                            'total_stock' => $product['total_stock'] + $detail['quantity'],
                        ]);
                    }
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 0
                    ]);
                }
            }

            Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->update([
                'order_status' => 'canceled'
            ]);
            return response()->json(['message' => 'Order canceled'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => 'not found!']
            ]
        ], 401);
    }

    public function update_payment_method(Request $request)
    {
        if (Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->first()) {
            Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->update([
                'payment_method' => $request['payment_method']
            ]);
            return response()->json(['message' => 'Payment method is updated.'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => 'not found!']
            ]
        ], 401);
    }

    public static function sendInvoicePdfToAdminAndUser($orderId)
    {
        ini_set('max_execution_time', 560);

        $order = Order::with([
            'customer', 'details', 'delivery_address',
        ])->find($orderId);

        $data = [];
        $data['order'] = $order;

        $view = 'admin-views.order.print-invoice-pdf';
        $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ])
            ->setPaper('A4', 'portrait')
            ->loadView($view, $data);

        $fileName = 'invoice-' . $order->id . '.pdf';
        $path = storage_path('app/public/invoices/pdf/' . $fileName);

        $directory = storage_path('app/public/invoices/pdf');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $pdf->save($path);

        // Create a temporary URL for the PDF file
        $url = asset('storage/app/public/invoices/pdf/' . $fileName);

        $customer = $order->customer;

        $userName = $customer->f_name . " " . $customer->l_name;

        $admin = Admin::find(1);

        $twilioService = new TwilioService();
        $twilioService->sendMedia($order->customer->phone, $userName, $fileName);
//        $twilioService->sendMedia($admin->phone, $url, $message);
    }

}
