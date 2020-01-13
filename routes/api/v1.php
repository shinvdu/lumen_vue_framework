<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
], function ($api) {
    // Auth

    // 获取微信js接口
    $api->post('base/wechat', [
        'as' => 'base.wechat',
        'uses' => 'BaseController@getWechatJsSign',
    ]);

    // 获取测试jwt token
    $api->get('auth/test_token/{pass}', [
        'as' => 'auth.test',
        'uses' => 'AuthController@getTestAuthBearer',
    ]);

    // 依据OpenID获取User
    $api->post('v1/ecp', [
        'as' => 'platform.account',
        'uses' => 'Platform\AccountController@getUserByOpenID'
    ]);

    // need authentication
    $api->group(['middleware' => 'ac_auth'], function ($api) {
        /*
         * 对于authorizations 并没有保存在数据库，所以并没有id，那么对于
         * 刷新（put) 和 删除（delete) 我没有想到更好的命名方式
         * 所以暂时就是 authorizations/current 表示当前header中的这个token。
         * 如果 tokekn 保存保存在数据库，那么就是 authorizations/{id}，像 github 那样。
         */
        // 验证验证码，获取jwt token
        $api->post('auth/verifysms', [
            'as' => 'auth.verifysms',
            'uses' => 'AuthController@verifySMS',
        ]);
        // SMS
        // 发送验证码
        $api->post('auth/sendsms', [
            'as' => 'auth.sendsms',
            'uses' => 'AuthController@sendSMS',
        ]);

        // 获取当前用户信息
        $api->get('users', [
            'as' => 'user.show',
            'uses' => 'UserController@getUserInfo',
        ]);
    });
});
