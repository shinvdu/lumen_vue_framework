<?php

namespace App\CodeMachine\Cache;

use App\CodeMachine\Contracts\CacheContract;
use Illuminate\Support\Facades\DB;

class MysqlCacheUtil implements CacheContract
{
    /**
     * 设置缓存，expire为timestamp
     *
     * @param $cid
     * @param $data
     * @param null $expire
     */
    public function set($cid, $data, $expire = null)
   {
       // 判断有没有缓存过有就先删除.
       $row = DB::table('cache')->where('cid', $cid)->first();
       if ($row) {
           $this->clear($cid);
       }

       $serialized = 0;

       if (!is_string($data)) {
           $data = serialize($data);
           $serialized = 1;
       }
       // 默认缓存过期时间.
       $default_expire = strtotime('+1 day');

       if (!$expire) {
          $expire = $default_expire;
       }

       $newrow = [
           'cid' => $cid,
           'value' => $data,
           'expeire' => $expire,
           'is_serialized' => $serialized
       ];

       DB::table('cache')->insert($newrow);
   }

   public function get($cid)
   {
       $row = DB::table('cache')->where('cid', $cid)->first();
       if (!$row) {
           return false;
       }

       if (time() > $row->expeire) {
           $this->clear($cid);
           return false;
       }

       if ($row->is_serialized == 1) {
           $row->value = unserialize($row->value);
       }

       return $row->value;
   }

   public function clear($cid)
   {
       DB::table('cache')->where('cid', $cid)->delete();
   }

   public function refresh()
   {
       // TODO: Implement refresh() method.
   }

}
