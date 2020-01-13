<?php

namespace App\Http\Controllers;

use App\CodeMachine\Contracts\ApiContract;
use App\CodeMachine\Contracts\CacheContract;
use App\Http\Controllers\Api\V1\AuthController;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Cookie as SCookie;
use Illuminate\Http\Request;
use Log;
use Exception;
use App\Models\ProductCode;
use Dingo\Api\Routing\Helpers;


class IndexController extends BaseController
{
    // 接口帮助调用
    use Helpers;

    /**
     * 请求微信oauth获取openid
     *
     * @return \Closure
     */
    public function redirOauth(Request $request)
    {
        if (env('RESTRICT')) {
            // 开启访问验证
            if ($request->get('restrict') != env('RESTRICT')) {
                die('Restrict Access enabled');
            }
        }

        $vendor_bundle_md5 = base_path() . '/public/js/client-vendor-bundle.js';
        $client_bundle_md5 = base_path() . '/public/js/client-bundle.js';
        $css_md5 =  base_path() . '/public/js/styles.css';
        $jweixin_md5 = base_path() . '/public/jweixin-1.2.0.js';

        header('X-Frame-Options: deny');

        header('Cache-Control:no-cache,must-revalidate');
        header('Pragma:no-cache');
        header("Expires:0");
        return view('index', [
            'md5' => [
                'vendor_bundle_md5' => hash_file('md5', $vendor_bundle_md5),
                'client_bundle_md5' => hash_file('md5', $client_bundle_md5),
                'css_md5' => hash_file('md5', $css_md5),
                'jweixin_md5' => hash_file('md5', $jweixin_md5)
            ]
        ]);

    }

    /**
     * gz压缩public/js文件夹下面的资源
     * @param $file_name
     */
    public function getGzFile($file_name)
    {
        ob_start(function ($content) {
            if(!headers_sent()&&extension_loaded("zlib") &&strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip"))
            {
                $content = gzencode($content,2);
                header("Content-Encoding: gzip");
                header("Vary: Accept-Encoding");
                header("Content-Length: ".strlen($content));
            }
            return $content;
        });

        $content = file_get_contents( base_path() . '/public/js/' . $file_name);
        print $content;
        ob_flush();
        flush();
    }

    /**
     *测试接口，直接登录
     *
     * @param \App\CodeMachine\Contracts\ApiContract   $apiClient
     * @param \App\CodeMachine\Contracts\CacheContract $cache_util
     *
     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector
     */
    public function getTestGrant(ApiContract $apiClient, CacheContract $cache_util, Request $request)
    {
        $uid = $request->get('uid');
        $auth_ctl = new AuthController($apiClient);
        $bearer_info = $auth_ctl->getTestAuthBearer($cache_util, 'xppxjCI5', $uid);
        $bearer_info = $bearer_info['data'];

        return redirect('/' . env('QUERY_STR'))->withCookie(
            new SCookie('token', $bearer_info['token'], time() + 60 * 60, $path = '/', $domain = null, $secure = false, $httpOnly = false, $raw = FALSE
            )
        );
    }

}
