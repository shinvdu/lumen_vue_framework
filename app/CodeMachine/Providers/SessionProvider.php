<?php

namespace App\CodeMachine\Providers;

use Illuminate\Support\ServiceProvider;
use App\CodeMachine\Platform\Session;

class SessionProvider extends ServiceProvider
{
    public function register()
    {
        // 使用singleton绑定单例.
        $this->app->singleton('App\CodeMachine\Platform\Session', function($app) {
            $config = config('vue_pro.session');
            return new Session($config);
        });
    }
}
