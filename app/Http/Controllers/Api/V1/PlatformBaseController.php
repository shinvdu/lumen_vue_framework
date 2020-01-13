<?php

namespace App\Http\Controllers\Api\V1;

use App\CodeMachine\Contracts\ApiContract;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Dingo\Api\Exception\ValidationHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use EasyWeChat\Foundation\Application;
use App\Models\AuthAccount;
use Log;
use Exception;

class PlatformBaseController extends BaseController{

    /**
     * 初始化api客户端
     *
     * @param \App\CodeMachine\Contracts\ApiContract $apiClient
     */
    public function __construct(ApiContract $apiClient = null){

        $postBaseContent = file_get_contents('php://input');
        $platform = app(\App\CodeMachine\Platform\PlatformBase::class);
        $platform->set_data($postBaseContent);
        parent::__construct($apiClient);

    }

}
