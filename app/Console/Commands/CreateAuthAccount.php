<?php

namespace App\Console\Commands;

use App\Models\AuthAccount;
use Illuminate\Console\Command;

class CreateAuthAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:auth_app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a auth api account';

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
        $app = $this->ask('App Name');

        $v = $this->gen_oauth_creds();
        // print_r($v);
        $n = [
            'app_id' => $v['key'],
            'app_name' => $app,
            'app_screct' => $v['secret'],
            'expeire' => 0,
        ];
        // print_r($n);
        $user = AuthAccount::create($n);

        $this->info('Success');
        $this->line('app_name: '.$app);
        $this->line('app_id: '. $v['key']);
        $this->line('app_screct: '.$v['secret']);
    }

    public function gen_oauth_creds() {
        return array(
            'key' => substr(sha1(uniqid()),0, 25),
            'secret' => substr(sha1(uniqid(100000, 999999) . rand()),0, 25) . substr(sha1(uniqid(100000, 999999) . rand()),0, 25) . substr(sha1(uniqid(100000, 999999) . rand()),0, 25)
        );
    }    
}
