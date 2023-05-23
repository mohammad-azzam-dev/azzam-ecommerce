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

    public function sendMedia($to, $name, $body)
    {
        if ($this->config['status'] == 1 && $this->accountSid && $this->authToken && $this->twilioPhoneNumber) {

            $client = new Client($this->accountSid, $this->authToken);

            $client->messages->create(
                "whatsapp:$to",
                [
                    'from' => "whatsapp:{$this->twilioPhoneNumber}",
                    "messagingServiceSid" => "MG8eeba5bab30b39434afbb8a2b6dddbde",
                    "contentSid" => "HX707b36b6bfb99763fbb47b23c6cab690",
                    "contentVariables" => json_encode([
                        "1" => $name,
                    ]),
                    "body" => $body,
                ]
            );

        }
    }

    public function call($from, $to, $url)
    {
        $client = new Client($this->accountSid, $this->authToken);

        $client->calls->create(
            $to,
            $from,
            [
                'url' => $url,
                'method' => 'GET',
            ]
        );
    }
}
