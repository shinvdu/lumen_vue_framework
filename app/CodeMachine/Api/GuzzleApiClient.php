<?php

namespace App\CodeMachine\Api;

use App\CodeMachine\Contracts\ApiContract;
use App\CodeMachine\Contracts\CacheContract;
use App\CodeMachine\Exceptions\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class GuzzleApiClient implements ApiContract
{
    private $base_uri;
    private $timeout;
    private $cache_util;

    private $client_instance = null;

    /**
     * 设置基本参数，cache repo
     *
     * GuzzleClient constructor.
     * @param string $_timeout
     * @param string $_base_uri
     * @param CacheContract $_cache_util
     * @internal param null $_cache_repo
     */
    public function __construct($_timeout, $_base_uri, CacheContract $_cache_util = null)
    {
        $this->base_uri = $_base_uri;
        $this->timeout = $_timeout;
        $this->cache_util = $_cache_util;

        $this->client_instance = new Client([
            'base_uri' => $this->base_uri,
            // 'proxy' => 'http://127.0.0.1:8888',
            'timeout' => $this->timeout
        ]);
    }

    public function getInstance()
    {
        return $this->client_instance;
    }

    /**
     * 清除bom头
     */
    public function clearBom($data)
    {
        return preg_replace('/\xEF\xBB\xBF/', '', $data);
    }

    /*
     * 测试获取cookie token
     */
    public function getCookieToken($renew = false) {
        $config = config('vue_pro.oauth');
        $credential = $config['credential'];

        $oauth_url = $config['token_url'];
        $token_cid = 'wechat_oauth_token';

        if (($cache_value = $this->cache_util->get($token_cid)) && !$renew) {
            $token_json = json_decode($cache_value, TRUE);
            $token = $token_json['access_token'];
            $token_type = $token_json['token_type'];
            return $token_type . ' ' . $token;
        }

        try {
            $res = $this->getInstance()->request('POST', $oauth_url, [
                'form_params' => $credential,
            ]);
            $res = $res->getBody()->getContents();
        } catch (ClientException $clientException) {
            throw new ApiException($clientException->getMessage(), 500);
        }

        $res = preg_replace('/\xEF\xBB\xBF/', '', $res);
        $token_json = json_decode($res, true);
        $this->cache_util->set($token_cid, $res, time() + $token_json['expires_in']);

        $token = $token_json['access_token'];
        $token_type = $token_json['token_type'];
        $auth_header_token = $token_type . ' ' . $token;
        return $auth_header_token;

    }

    /**
     * 发送api请求
     *
     * @param        $uri
     * @param string $method
     * @param array  $parameters
     * @param        $cache_exp_time
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \App\CodeMachine\Exceptions\ApiException
     * @internal param array $cache_setting
     * @internal param array $cache
     */
    public function request($uri, $method = 'GET', array $parameters = [], $cache_exp_time = null)
    {
        // 先取得token，再请求中加上认证header.
        $token = $this->getCookieToken();

        if (!$token) {
            // 返回异常
            throw new ApiException('Guzzle Api client cannot get oauth token');
        }
        // $parameters['query']['XDEBUG_SESSION_START'] = 1;
        if ($method == 'GET') {
            $req_item = [];
            if (isset ($parameters['query']) && is_array($parameters['query'])) {
                $cid = 'de_' . $uri .'_'. serialize($parameters['query']);
            }
            else {
                $cid = 'de_' . $uri .'_'. join('+', $req_item);
            }
        }
        else {
            $cid = 'de_' . $uri . '_' . json_encode($parameters);
        }
        $cid = md5($cid);

        $cached_value = $this->cache_util->get($cid);

//         从缓存取数据.
//         成功返回结果，失败继续从de获取.
        if ($cached_value) {
            if ($json_rs = json_decode($cached_value, true))
                return $json_rs;
            else
                return $cached_value;
        }

        $parameters['headers']['Authorization'] = $token;
        // 请求错误日志在/storage/logs/lumen.log
        try {
            $response = $this->getInstance()->request($method, $this->base_uri . $uri, $parameters);
        }
        catch (ClientException $e) {
            $err_code = $e->getCode();
            if ($err_code === 401) {
                // 尝试重新取得token
                Log::info('Trying reget backend oauth token, time now is :' . time());
                $new_token = $this->getCookieToken(true);
                $parameters['headers']['Authorization'] = $new_token;
                // 重新请求数据
                $response = $this->getInstance()->request($method, $this->base_uri . $uri, $parameters);
            }
            else {
                throw new ApiException($e->getMessage());
            }
        }

        $data = $response->getBody()->getContents();
        //todo bom头
        $data = $this->clearBom($data);
        // 如果设置为被需要缓存.
        $json_arr = json_decode($data, true);
        if (is_numeric($cache_exp_time) && $json_arr['state'] == 1){
            $this->cache_util->set($cid, $data, $cache_exp_time);
        }

        return $json_arr;
    }

    /**
     * 异步请求
     * @param        $uri
     * @param string $method
     * @param array  $parameters
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     * @throws \App\CodeMachine\Exceptions\ApiException
     */
    public function asyncRequest($uri, $method = 'GET', array $parameters = [])
    {
        $token = $this->getCookieToken();
        if (!$token) {
            // 返回异常
            throw new ApiException('Guzzle Api client cannot get oauth token', 10);
        }

        $parameters['headers']['Authorization'] = $token;
        $promise = $this->getInstance()->requestAsync($method, $this->base_uri . $uri, $parameters);

        return $promise;
       // $promise->wait();
    }

}
