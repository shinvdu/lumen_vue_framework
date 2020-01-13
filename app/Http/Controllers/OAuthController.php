<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CodeMachine\Contracts\ApiContract;
use Laravel\Lumen\Routing\Controller as BaseController;
use EasyWeChat\Foundation\Application;
use Symfony\Component\HttpFoundation\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Log;
use Exception;


class OAuthController extends BaseController
{

    /**
     * 发起Oauth鉴权获取用户openid
     * @param \EasyWeChat\Foundation\Application $weapp
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirOauth(Application $weapp, Request $request) {
        
        if (env('OPENID_REDIR_URL')) {
            // 第三方鉴权跳转
            return redirect(env('OPENID_REDIR_URL'));
        }

        $response = $weapp->oauth->scopes(['snsapi_base'])
            ->redirect();

        $response->send();
    }

    /**
     * 微信oauth回调处理页
     * @param \EasyWeChat\Foundation\Application $weapp
     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector
     */
    public function OauthCallback(Application $weapp, Request $request, ApiContract $apiClient) {
        if ($request->get('openId')) {
            // 如果回调地址包含openId直接设置，否则通过微信取得
            $openId = $request->get('openId');
        }
        else {
            $oauth = $weapp->oauth;
            $user = $oauth->user();
            $user->toArray();
            $openId = $user['id'];

            if (!$openId) {
                echo 'wx id获取失败';
                exit;
            }
        }
        // Log::info($_SERVER['DOCUMENT_ROOT']);

        // 处理不再需要验证码登陆的逻辑
        $res = $apiClient->request('/api/get/simple_user', 'GET', [
            'query' => [
                'openid' => $openId
            ]
        ]);

        if ($res['state'] == 1) {
            if (is_array($res['data']) && count($res['data']) === 1) {
                $account = array_pop($res['data']);
                $uid = $account['uid'];
                $name = $account['name'];
                $phone = $account['name'];
                $status = $account['status'];
                $user_payload = ['phone' => $phone, 'user_info' => ['uid' => $uid, 'name' => $name]];
                $payload = JWTFactory::sub(2)->aud('code_machine')->user($user_payload)->make();
                $main_token = JWTAuth::encode($payload);
            }
        }

        // 生成一个token放cookie，登录的时候先验证jwt再把openid传到后端判断
        $payload = JWTFactory::sub(1)->aud('code_machine')->wx(['wxid' => $openId])->make();
        $token = JWTAuth::encode($payload);
        // Store user openid for a month stead of 20 minites.
        if (isset($main_token) && $main_token) {
            return redirect('/' . env('QUERY_STR'))->withCookie(new Cookie('wxid', $token, time() + 60 * 60, $path = '/', $domain = null, $secure = false, $httpOnly = false))->withCookie(new Cookie('token', $main_token, time() + 2 * 60, $path = '/', $domain = null, $secure = false, $httpOnly = false));
        }else{
            return redirect('/' . env('QUERY_STR'))->withCookie(new Cookie('wxid', $token, time() + 60 * 60, $path = '/', $domain = null, $secure = false, $httpOnly = false));

        }
    }
}