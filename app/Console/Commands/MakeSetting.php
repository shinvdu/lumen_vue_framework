<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class MakeSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setting:set {key} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set key = value, example: php artisan setting:set appid x32xxxxxxx3';

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
        $value = $this->argument('value');
        if (!($key || $value )) {
            $this->info('Failed');
            $this->line('key and value is required. ');
        }
        $s = Setting::where(['key' => $key])->first();
        if ($s) {
            $s->value = $value;
            $s->save();
        }else{
            $n = [
                'key' => $key,
                'value' => $value,
                'created' => time(),
            ];
            $setting = Setting::create($n);
        }
        $this->info('Success');
        $this->line('key: '. $key);
        $this->line('value: '. $value);
    }

}
