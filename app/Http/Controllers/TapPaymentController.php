<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tap\Tap;
use Tap\Charge;

class TapPaymentController extends Controller
{
    protected $tap;

    /**
     * Setup Tap payment
     */
    public function __construct()
    {
        $tapConfig = Helpers::get_business_settings('tap');

        abort_if(!$tapConfig || !filter_var($tapConfig['status'], FILTER_VALIDATE_BOOLEAN), 403);

        Tap::setApiKey($tapConfig['api_key']);
        Tap::setVerifySslCerts(strtolower(env('APP_ENV')) == 'live');
    }

    /**
     * Get payment link
     *
     * @param Request $request
     * @return array
     */
    public function pay(Request $request)
    {
        $order = Order::with(['details'])->where(['id' => session('order_id')])->firstOrFail();

        DB::beginTransaction();

        try {
            $callbackURL = route('payments.tap.callback');
            $charge = Charge::create([
                'amount' => $order['order_amount'],
                'currency' => Helpers::currency_code(),
                'source' => [
                    'id' => 'src_all', // Multiple payment methods related to differnt countries
                    // 'id' => 'src_card', // Only Master/Visa card
                ],
                'customer' => [
                    'id' => '',
                    'first_name' => $order->customer['f_name'],
                    'email' => $order->customer['email'],
                ],
                'redirect' => [
                    'url' => $callbackURL
                ]
            ]);

            $paymentLink = $charge->transaction ? $charge->transaction->url ?? null : null;

            $order->update(['transaction_reference' => $charge->id]);

            DB::commit();

            return redirect()->to($paymentLink);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Get Tap payment information
     * 
     * @return \Illuminate\Http\Response
     */
    public function callback()
    {
        $paymentId = request('tap_id');

        $tapPayment = Charge::retrieve($paymentId);


        $order = Order::where('transaction_reference', $paymentId)->where('payment_status', '!=', 'paid')->firstOrFail();

        /**
         * INITIATED, IN_PROGRESS, ABANDONED, CANCELLED, FAILED, DECLINED, RESTRICTED, CAPTURED, VOID, TIMEDOUT, UNKNOWN
         * Initial status will be INITIATED or FAILED, DECLINED, RESTRICTED, CAPTURED
         * If the charge status is INITIATED then Payer should be redirected to transaction url to perform the transaction
         */

        if ($tapPayment->status == 'CAPTURED') {
            $order->update(['order_status' => 'confirmed', 'payment_status' => 'paid']);

            $fcm_token = $order->customer->cm_firebase_token;
            $value = Helpers::order_status_update_message('confirmed');
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }

            if ($order->callback != null) {
                return redirect($order->callback . '/success');
            } else {
                return \redirect()->route('payment-success');
            }
        } else if ($tapPayment->status == 'INITIATED') {
            $paymentLink = $tapPayment->transaction ? $tapPayment->transaction->url ?? null : null;

            return redirect()->to($paymentLink);
        } else {
            if ($order->callback != null) {
                return redirect($order->callback . '/fail');
            } else {
                return \redirect()->route('payment-fail');
            }
        }
    }
}
