<?php

namespace App\Http\Controllers\Api\V1;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\CodeMachine\Contracts\CacheContract;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;


class UserController extends BaseController
{

    /**
     * 获得用户信息
     * api客户端
     *
     * @param \Illuminate\Http\Request $request
     */
    public function getUserInfo(Request $request)
    {
        $payload = $request->get('user_payload');
        // store request
        $token = $request->input('token');
        $uid = $payload['user']['user_info']['uid'];

        $query = [
            'uid' => $uid
        ];
        if ($token) {
            $query['token'] = $token;
        }

        $res = $this->api_client->request('/api/get/user_info', 'GET', [
            'query' => $query
        ]);
       
        if ($res['state'] == 1) {

            return $this->response->array($res['data']);
        }
        else {
            throw new unprocessableentityhttpexception($res['message']);
        }
    }

}
