<?php

namespace App\CodeMachine\Contracts;

interface ApiContract
{
    public function request($uri, $method = 'get', array $parameters = [], $cache_exp_time);
}