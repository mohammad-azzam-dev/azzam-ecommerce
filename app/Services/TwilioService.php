<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $accountSid;
    protected $authToken;
    protected $twilioPhoneNumber;

    public function __construct()
    {
        $this->accountSid = config('services.twilio.account_sid');
        $this->authToken = config('services.twilio.auth_token');
        $this->twilioPhoneNumber = config('services.twilio.phone_number');
    }

    public function sendWhatsAppMessage($to, $message)
    {
        $client = new Client($this->accountSid, $this->authToken);

        $client->messages->create(
            "whatsapp:$to",
            array(
                'from' => "whatsapp:{$this->twilioPhoneNumber}",
                'body' => $message,
            )
        );
    }
}
