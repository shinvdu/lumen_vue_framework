<?php

namespace App\CodeMachine\Providers;

use Illuminate\Support\ServiceProvider;
use App\CodeMachine\Platform\PlatformBase;


class PlatformProvider extends ServiceProvider
{
    public function register()
    {
        // 使用singleton绑定单例.
        $this->app->singleton('App\CodeMachine\Platform\PlatformBase', function($app) {
            return new PlatformBase();
        });
    }
}