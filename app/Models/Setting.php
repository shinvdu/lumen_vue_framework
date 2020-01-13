<?php

namespace App\Models;

use App\Facades\Util;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Log;

/**
 * @property string    app_id  
 * @property string app_name  
 * @property string app_screct
 * @property integer   expire
 */
class Setting extends Model
{

    protected $fillable = ['id', 'key', 'value', 'created'];

    public $timestamps = false;

    // static function activity_durations($coupon_id){
    // 	static $coupon_durations;
    // 	if (!$coupon_durations) {
    // 		$coupon_durations = [];
    // 		$s = Setting::where('key', 'like', 'coupon_duration_%')->get();
    // 		foreach ($s as $cs) {
    // 			$id = (int)str_replace('coupon_duration_', '', $cs->key);
    // 			$coupon_durations[$id] = $cs->value;
    // 		}
    // 	}
    // 	// print_r($coupon_durations);
    // 	if (isset($coupon_durations[$coupon_id])) {
    // 		return $coupon_durations[$coupon_id];
    // 	}else{
    // 		return '';
    // 	}
    // }
}
