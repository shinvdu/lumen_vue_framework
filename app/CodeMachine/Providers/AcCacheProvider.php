<?php

namespace App\CodeMachine\Providers;

use App\CodeMachine\Cache\CacheManager;
use Illuminate\Support\ServiceProvider;

class AcCacheProvider extends ServiceProvider
{
    public function register()
    {
        // 使用singleton绑定单例.

        $this->app->singleton('App\CodeMachine\Contracts\CacheContract',function($app) {
            $config = config('vue_pro.cache_config');
            return CacheManager::getClient($config['client']);
        });
    }
}