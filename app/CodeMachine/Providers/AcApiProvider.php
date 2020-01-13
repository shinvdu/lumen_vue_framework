<?php

namespace App\CodeMachine\Providers;

use App\CodeMachine\Api\ApiClientManager;
use Illuminate\Support\ServiceProvider;

class AcApiProvider extends ServiceProvider
{
    public function register()
    {
        // 使用singleton绑定单例.

        $this->app->singleton('App\CodeMachine\Contracts\ApiContract', function($app) {
            $config = config('vue_pro.api_config');
            // 获取Cache Service Provider
            $cache_ins = $app['App\CodeMachine\Contracts\CacheContract'];
            return ApiClientManager::getClient($config['client'], $cache_ins);
        });
    }
}