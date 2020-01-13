<?php

namespace App\Http\Controllers\Api\V1;

use App\CodeMachine\Contracts\ApiContract;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Dingo\Api\Exception\ValidationHttpException;
use EasyWeChat\Foundation\Application;

class BaseController extends Controller
{
    // 接口帮助调用
    use Helpers;

    protected $api_client;

    /**
     * 初始化api客户端
     * UserController constructor.
     *
     * @param \App\CodeMachine\Contracts\ApiContract $apiClient
     */
    public function __construct(ApiContract $apiClient = null)
    {
        $this->api_client = $apiClient;
    }


    /**
     * 获取微信jssdk接口签名
     *
     * @param \EasyWeChat\Foundation\Application $weapp
     * @param \Illuminate\Http\Request           $request
     *
     * @return
     */
    public function getWechatJsSign(Application $weapp, Request $request)
    {
        $sign_url = $request->input('sign_url');
        $js = $weapp->js;

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        // $js->setUrl($sign_url);
        $js->setUrl($http_type . $_SERVER['HTTP_HOST'] . '/' . env('QUERY_STR'));

        $we_arr = $js->config(['onMenuShareQQ', 'onMenuShareWeibo', 'scanQRCode', 'chooseImage', 'uploadImage'], $debug = env('WX_DEBUG'), $json = FALSE);
        return $this->response->array($we_arr);
    }

}
