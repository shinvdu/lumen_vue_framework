<?php

namespace App\CodeMachine\Api;

class ApiClientManager
{
    public function getConfig()
    {
        return config('ac_http.config');
    }

    public static function getClient($type, $cache_ins)
    {
        $config = config('vue_pro.api_config');
        $class_path = __NAMESPACE__ . '\\' . $type . 'ApiClient' ;
        $class = new \ReflectionClass($class_path);
        return $class->newInstance($config['timeout'], $config['base_uri'], $cache_ins);
    }
}