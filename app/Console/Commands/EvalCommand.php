<?php

namespace App\Console\Commands;

// use App\Models\Setting;
use Illuminate\Console\Command;

class EvalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eval:command {string}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'eval specify command, example: php artisan eval:command "print(3243);"';

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
     * @return mixed
     */
    public function handle()
    {
        $command = $this->argument('string');
        eval($command);
    }

}
