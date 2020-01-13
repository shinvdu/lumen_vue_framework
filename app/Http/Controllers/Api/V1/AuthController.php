<?php

namespace App\Http\Controllers\Api\V1;

use App\CodeMachine\Contracts\CacheContract;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthController extends BaseController
{

    /**
     *
     * 测试自定义获取jwt token
     *
     * @param array $user_payload
     *
     * @return mixed
     */
    public function sign_token(array $user_payload)
    {

        // 成功验证了手机验证码以后，传入用户手机号，创建token给客户端
        // 检查后端openId是否与当前一致，如果openId没有值则认为首次登陆提交该值
        // 不一致则进行解绑操作

        $payload = JWTFactory::sub(2)->aud('code_machine')->user($user_payload)->make();
        $token = JWTAuth::encode($payload);
        $result['data'] = [
            'token' => (string)$token,
            'expired_at' => Carbon::now('Asia/Shanghai')->addMinutes(config('jwt.ttl'))->toDateTimeString(),
            'refresh_expired_at' => Carbon::now('Asia/Shanghai')->addMinutes(config('jwt.refresh_ttl'))->toDateTimeString(),
        ];
        return $result;
    }


    /**
     * 发送验证码
     * @param \Illuminate\Http\Request $request
     */
    public function sendSMS(Request $request)
    {
        /**
         * 验证手机号规则
         */

        \Validator::extend('mobile', function($attribute, $value, $parameters)
        {
            return preg_match('/^0?(1[0-9])[0-9]{9}$/', $value);
        });

        $validator = \Validator::make($request->input(), [
            'phone' => 'required|mobile',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $phoneNumber = $request->get('phone');

        //手机号在前wxid在后sha1方法加密

        $time = time();
        $token = sha1($phoneNumber . $time . env('REG_SHA_TOKEN'));
        $response = $this->api_client->request('/api/get/send_message', 'GET', [
            'query' => ['phone' => $phoneNumber]
        ]);

        $res_arr = $response;
        $res_arr['reg_token'] = $token;
        $res_arr['time'] = $time;

        if ($res_arr['state'] != 1) {
            throw new AccessDeniedHttpException($res_arr['message']);
        }

        return $this->response->array($res_arr, true);
    }


    /**
     * 验证短信，返回jwt token
     *
     * @param \Illuminate\Http\Request $request
     */
    public function verifySMS(Request $request)
    {
        $verify_number = $request->input('verify_number');
        $phone_number = $request->input('phone_number');

        $query = ['phone' => $phone_number, 'code' => $verify_number];

        $response = $this->api_client->request('/api/get/validate_message', 'GET', [
            'query' => $query,
        ]);

        if ($response['state'] != 1){
            throw new AccessDeniedHttpException($response['message']);
        }

        $uid = $response['data']['uid'];

        $res = $this->api_client->request('/api/get/user_info', 'GET', [
            'query' => [
                'uid' => $uid
            ]
        ]);
        if ($res['state'] == 1) {
            $simple_userinfo['uid'] = $uid;
            $simple_userinfo['name'] = $response['data']['uid'];
            // 把手机号放进用户payload
            $bearer_info = $this->sign_token(['phone' => $phone_number, 'user_info' => $simple_userinfo]);
            return $this->response->array($bearer_info)->setStatusCode(201);
        }
        else {
            if(isset($res['code']) && $res['code'] === 1){
                throw new AccessDeniedHttpException(1);
            }
            else{
                throw new AccessDeniedHttpException($res['message']);
            }
        }
    }

    /**
     * 获取测试用jwt token，需要通过认证
     *
     * @param \App\CodeMachine\Contracts\CacheContract $cache_util
     *
     * @param                                               $pass
     *
     * @return mixed
     */
    public function getTestAuthBearer(CacheContract $cache_util, $pass, $uid = null)
    {
        if (!env('API_TEST_ENABLE')) {
            throw new UnauthorizedHttpException('Acuvue pro', 'Get Test Api Failed');
        }else{
            if ($uid) {
                $test_uid = $uid;
            }else {
                $test_uid = env('TEST_UID');
            }
            $res = $this->api_client->request('/api/get/user_info', 'GET', [
                'query' => [
                    'uid' => $test_uid
                ]
            ]);
            if ($res) {
                $data = [
                    'uid' => $test_uid , 
                    'name' =>  $res['data']['name'],
                    'roles' => $res['data']['roles'],
                ];
                $bearer = $this->sign_token(['phone' => $res['data']['name'], 'user_info' => $data]);
            }else{
                $data = [
                    'uid' => $test_uid ,
                    'name' => 'xxxxx',
                    'roles' => [ 2 =>  'authenticated user' ,
                    3 =>  'administrator' ,
                    4 =>  'Api' ]
                ];
                $bearer = $this->sign_token(['phone' => '18888888888', 'user_info' => $data]);
            }
            return $bearer;
        }
    }

}
