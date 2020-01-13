<?php

namespace App\CodeMachine\Platform;

use Log;
use App\CodeMachine\Platform\SessionRedis;

/**
* 
*/
class Session{
    function __construct($config){
        $handler = new SessionRedis($config);
        session_set_save_handler($handler);
    }
}

