<?php

namespace App\Http\Controllers;

use App\Model\Order;
use App\User;
use Illuminate\Http\Request;

use function App\CentralLogics\translate;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->firstOrFail();

        if ($order->payment_status == 'paid') {
            die(translate('payment_already_made_for_order'));
        }

        if (session()->has('payment_method') == false) {
            session()->put('payment_method', 'ssl_commerz_payment');
        }

        if ($request->has('callback')) {
            $order->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->order_id);

        $customer = User::find($request['customer_id']);


        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
            return view('payment-view');
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);

    }

    public function success()
    {
        if (session()->has('callback')) {
            return redirect(session('callback') . '/success');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        if (session()->has('callback')) {
            return redirect(session('callback') . '/fail');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }
}
