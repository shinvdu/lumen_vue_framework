<?php

namespace App\Console\Commands;

// use App\Models\Setting;
use Illuminate\Console\Command;

class EvalFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eval:file {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'eval specify file, example: php artisan eval:file "/app/file.php"';

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
        $file = $this->argument('file');
        require($file);
    }

}
