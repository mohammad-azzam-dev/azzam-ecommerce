<?php

namespace App\Console\Commands;

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
        $twilioService->call('133', '+96171739279', $url);

    }
}
