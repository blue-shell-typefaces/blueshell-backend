<?php

namespace App\Console\Commands;

use App\Models\Family;
use Illuminate\Console\Command;

class GenerateInstance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:instance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate instance';

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
        $familyPath = Family::first()->getFullPath();
        $script = config('app.instancer');

        `$script "$familyPath" wght=100`;

        return Command::SUCCESS;
    }
}
