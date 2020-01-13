<?php

namespace App\CodeMachine\Contracts;

interface CacheContract
{
    public function set($cid, $data, $expire = '');

    public function get($cid);

    public function refresh();

    public function clear($cid);
}