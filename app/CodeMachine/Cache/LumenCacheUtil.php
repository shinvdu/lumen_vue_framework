<?php

namespace App\CodeMachine\Cache;

use App\CodeMachine\Contracts\CacheContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class LumenCacheUtil implements CacheContract
{
    /**
     * 设置缓存，expire为timestamp
     *
     * @param $cid
     * @param $data
     * @param null $expireat
     */
    public function set($cid, $data, $expireat = null)
    {
        $datatime = Carbon::createFromTimestamp($expireat);
        Cache::put($cid, $data, $datatime);
    }

    public function get($cid)
    {
        return Cache::get($cid);
    }

    public function clear($cid)
    {
        Cache::forget($cid);
    }

    public function refresh()
    {
        // TODO: Implement refresh() method.
    }

}
