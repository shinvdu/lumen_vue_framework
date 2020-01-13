<?php

namespace App\CodeMachine\Providers;

use Illuminate\Support\ServiceProvider;
use EasyWeChat\Foundation\Application;

class WechatProvider extends ServiceProvider
{
    public function register()
    {
        // 使用singleton绑定单例.
        $this->app->singleton('EasyWeChat\Foundation\Application', function($app) {
            $app->configure('vue_pro');
            $config = config('vue_pro.wechat_config');
            return new Application($config);
        });
    }
}