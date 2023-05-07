<?php

namespace App\Console\Commands;

use App\Model\TwilioCalls;
use App\Services\TwilioService;
use Illuminate\Console\Command;

class InitiateTwilioCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiate a twilio call';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $twilioService = new TwilioService();

        $url = url('twilio/call');

        $twilioNumbers = ['+17123838616', '+14752646644', '+17622426069'];

        $twilioCall = TwilioCalls::find(1);
        $lastUsedIndex = $twilioCall->last_used_index ?? 0;

        $nextIndex = ($lastUsedIndex + 1) % count($twilioNumbers);
        $nextTwilioNumber = $twilioNumbers[$nextIndex];

        $twilioService->call($nextTwilioNumber, '+96171739279', $url);

        $twilioCall->update([
            'last_used_index' => $lastUsedIndex,
        ]);
    }
}
