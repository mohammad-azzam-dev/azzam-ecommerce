<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;

class MyFatoorahPaymentController extends Controller
{
    protected $myFatoorah;
    protected $myFatoorahConfig;

    /**
     * create MyFatoorah object
     */
    public function __construct()
    {
        // The "countryCode" key must be one of (KWT, SAU, ARE, QAT, BHR, OMN, JOD, EGY).

        $myFatoorahConfig = Helpers::get_business_settings('myFatoorah');

        abort_if(!$myFatoorahConfig || !filter_var($myFatoorahConfig['status'], FILTER_VALIDATE_BOOLEAN), 403);

        $this->myFatoorahConfig = [
            'apiKey' => $myFatoorahConfig['api_key'],
            'countryCode' => $myFatoorahConfig['country_code'],
            'isTest' => filter_var($myFatoorahConfig['is_test'], FILTER_VALIDATE_BOOLEAN),
            // 'apiKey' => 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL',
            // 'countryCode' => 'KWT',
            // 'isTest' => true,
        ];

        $this->myFatoorah = new MyFatoorahPayment($this->myFatoorahConfig);
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
            $callbackURL = route('payments.myFatoorah.callback');

            $postFields = [
                'NotificationOption' => 'Lnk',
                'InvoiceValue'       => $order['order_amount'],
                'CustomerName'       => $order->customer['f_name'],
                'CallBackUrl'        => $callbackURL,
                'ErrorUrl'           => $callbackURL,
            ];

            $data = $this->myFatoorah->getInvoiceURL($postFields);

            $invoiceId   = $data['invoiceId'];
            $paymentLink = $data['invoiceURL'];

            // $order->update(['invoice_id' => $invoiceId]);

            DB::commit();

            return redirect()->to($paymentLink);

            // return [
            //     'success' => true,
            //     'data' => [
            //         'payment_link' => $paymentLink,
            //         'invoice_id' => $invoiceId,
            //     ]
            // ];
        } catch (Exception $e) {
            DB::rollBack();

            dd($e);

            throw $e;
        }
    }

    /**
     * Get MyFatoorah payment information
     * 
     * @return \Illuminate\Http\Response
     */
    public function callback()
    {
        try {
            $myFatoorahPayment = new MyFatoorahPaymentStatus($this->myFatoorahConfig);

            $data = $myFatoorahPayment->getPaymentStatus(request('paymentId'), 'PaymentId');

            $msg = '';
            if ($data->InvoiceStatus == 'Paid') {
                return redirect()->route('payment-success');

                $msg = 'Invoice is paid.';
            } else if ($data->InvoiceStatus == 'Failed') {
                return redirect()->route('payment-fail');

                $msg = 'Invoice is not paid due to ' . $data->InvoiceError;
            } else if ($data->InvoiceStatus == 'Expired') {
                return redirect()->route('payment-fail');

                $msg = 'Invoice is expired.';
            }

            return response()->json(['IsSuccess' => 'true', 'Message' => $msg, 'Data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['IsSuccess' => 'false', 'Message' => $e->getMessage()]);
        }
    }
}
