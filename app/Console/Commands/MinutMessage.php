<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MinutMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:minut';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call the Route To Check the appointment time';

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
        echo "this is my first scadular command";
        // return "this is my first scadular command";
    }
}
