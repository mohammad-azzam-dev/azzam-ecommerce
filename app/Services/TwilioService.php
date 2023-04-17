<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $config;
    protected $accountSid;
    protected $authToken;
    protected $twilioPhoneNumber;

    public function __construct()
    {
        $this->config = \App\CentralLogics\Helpers::get_business_settings('twilio');

        $this->accountSid = $this->config ? $this->config['sid'] : null;
        $this->authToken = $this->config ? $this->config['token'] : null;
        $this->twilioPhoneNumber = $this->config ? $this->config['phone_number'] : null;
    }

    public function sendWhatsAppMessage($to, $message)
    {
        if( $this->config['status'] == 1 && $this->accountSid && $this->authToken && $this->twilioPhoneNumber ) {

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

    public function sendMedia($to, $publicUrl)
    {
        if( $this->config['status'] == 1 && $this->accountSid && $this->authToken && $this->twilioPhoneNumber ) {

            $client = new Client($this->accountSid, $this->authToken);

            $client->messages->create(
                "whatsapp:$to",
                [
                    'from' => "whatsapp:{$this->twilioPhoneNumber}",
                    'body' => 'Here is your invoice',
                    'mediaUrl' => $publicUrl
                ]
            );

        }
    }
}
