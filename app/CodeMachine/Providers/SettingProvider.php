<?php

namespace App\CodeMachine\Providers;

use Illuminate\Support\ServiceProvider;
use App\CodeMachine\Setting\SettingHandler;

class SettingProvider extends ServiceProvider
{
    public function register()
    {
        // 使用singleton绑定单例.
        $this->app->singleton('App\CodeMachine\Setting\SettingHandler', function($app) {
            return new SettingHandler();
        });
    }
}
