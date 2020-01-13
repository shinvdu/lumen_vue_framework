<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Log;

class TokenAuth
{
    /**
     * 自定义身份验证，传token里的claim到request里进行下一步操作.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
            // Log::info(print_r($_SERVER, true));
        if (!isset($_SERVER['HTTP_AUTHORIZATION']) || empty(trim($_SERVER['HTTP_AUTHORIZATION']))) {
            Log::info(sprintf('请求异常: uri: %s',  $_SERVER['REQUEST_URI']));
            throw new UnauthorizedHttpException('Acuvue Pro', '请求异常');
            return $next($request);
        }else{
            // Log::info(sprintf('auth: %s, uri: %s', $_SERVER['HTTP_AUTHORIZATION'], $_SERVER['REQUEST_URI']));
            try {
                $payload = JWTAuth::parseToken()->getPayload();
                $user_payload = $payload;
            // 设置用户手机号等信息
                $request->attributes->add(compact('user_payload'));
            } catch (TokenExpiredException $e) {
                try {
                //JWTAuth::getToken();
                    $refreshed = JWTAuth::refresh();
                    $payload = JWTAuth::setToken($refreshed)->getPayload();
                    $user_payload = $payload;
                // 设置用户手机号等信息
                    $request->attributes->add(compact('user_payload'));
                // 响应设置新token的头，
                    header('refreshedtoken: ' . $refreshed);
                }
                catch (JWTException $e) {
                    Log::info('refresh expired token error: ' . $e->getMessage());
                    throw new UnauthorizedHttpException('Acuvue Pro', '会话请求异常，如重复出现，请访问<a href="/logout">退出</a>重试，谢谢。');
                }

            } catch (TokenInvalidException $e) {
                throw new UnauthorizedHttpException('Acuvue Pro', '验证信息格式无效');

            } catch (JWTException $e) {
                throw new HttpException($e->getMessage());
            }
            return $next($request);
        }

    }
}