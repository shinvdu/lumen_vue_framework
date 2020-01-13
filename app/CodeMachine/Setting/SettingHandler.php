<?php

namespace App\CodeMachine\Setting;

use App\Models\Setting;

use Log;
use Exception;

class SettingHandler {

    /**
     * 初始化api客户端
     *
     */
    public function __construct(){

    }

    public function get($key, $default = ''){
        $s = Setting::where(['key'  => $key])->first();
        if ($s) {
            return $s->value;
        }else{
            return $default;
        }
    }

    public function set($key, $value){
        try {
            $s = Setting::where(['key' => $key])->first();
            if ($s) {
                $s->value = $value;
                $s->save();
            }else{
                $n = [
                    'key' => $key,
                    'value' => $value,
                    'created' => time(),
                ];
                $setting = Setting::create($n);
            }            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}