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
 * @property integer   expeire
 */
class AuthAccount extends Model
{

    protected $fillable = ['app_id', 'app_name', 'app_screct', 'expeire'];

    public $timestamps = false;

}
