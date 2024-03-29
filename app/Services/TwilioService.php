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

    public function sendMedia($to, $userName, $fileName)
    {
        if ($this->config['status'] == 1 && $this->accountSid && $this->authToken && $this->twilioPhoneNumber) {

            $client = new Client($this->accountSid, $this->authToken);

            $client->messages->create(
                "whatsapp:$to",
                [
                    "messagingServiceSid" => "MG8eeba5bab30b39434afbb8a2b6dddbde",
                    'from' => "whatsapp:{$this->twilioPhoneNumber}",
                    "contentSid" => "HX9533c5d4bad900995302cf3d73a91d72",
                    "contentVariables" => json_encode([
                        "1" => $fileName,
                        "2" => $userName,
                    ]),
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
