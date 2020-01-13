<?php

namespace App\CodeMachine\Cache;

class CacheManager
{
    public static function getClient($type)
    {
        $class_path = __NAMESPACE__ . '\\' . $type . 'CacheUtil' ;
        $class = new \ReflectionClass($class_path);
        return $class->newInstance();
    }
}