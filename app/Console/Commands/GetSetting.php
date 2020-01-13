<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class GetSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setting:get {key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get key value, example: php artisan setting:set appid';

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
        $key = $this->argument('key');
        if (!$key) {
            $this->info('Failed');
            $this->line('key is required. ');
        }
        $s = Setting::where(['key' => $key])->first();
        if ($s) {
            $this->info('Success');
            $this->line('key: '. $key);
            $this->line('value: '. $s->value);
        }else{
            $this->warn('Failed, key not seted');
            $this->line('key: '. $key);
        }
    }

}
