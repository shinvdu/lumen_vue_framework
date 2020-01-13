<?php

namespace App\Http\Controllers\Api\V1\Platform;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\CodeMachine\Contracts\CacheContract;
use Log;
use Exception;
use App\Http\Controllers\Api\V1\PlatformBaseController;
use App\CodeMachine\Platform\PlatformBase;


class AccountController extends PlatformBaseController{

    /**
     * 依据OpenID获取USER信息
     * @param \Illuminate\Http\Request $request
     * @param                          $openid
     *
     * @return mixed
     */
    public function getUserByOpenID(Request $request, PlatformBase $platform)
    {
        if (!isset($platform->requestArray['OpenID'])) {
            throw new UnprocessableEntityHttpException('OpenID是必需的。');
        }
        $user =  $platform->getUserByOpenid($platform->requestArray['OpenID']);
        if (isset($user['error'])) {
            throw new UnprocessableEntityHttpException('获取ECP信息失败');
        }else{
            return $this->response->array($user);
        }
    }

    /**
     * 通过OpenID发送模板消息
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function sendWechatTemplate(Request $request, PlatformBase $platform)
    {
        if (!isset($platform->requestArray['Touser'])) {
            throw new UnprocessableEntityHttpException('Touser是必需的。');
        }
        
        $response = $platform->sendWechatTemplateMsg($platform->requestArray['Touser']);

        if ($response['errcode']) {
            throw new UnprocessableEntityHttpException($response['errmsg']);
        }else{
            return $this->response->array($response);
        }
    }

}
