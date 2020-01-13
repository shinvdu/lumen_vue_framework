<?php

namespace App\CodeMachine\Platform;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Models\AuthAccount;
use App\CodeMachine\Contracts\CacheContract;

use Log;
use Exception;

class PlatformBase {

    private             $DEFAULT_CHARSET = 'UTF-8';
    private             $METHOD_POST     = "POST";
    private             $METHOD_GET      = "GET";
    private             $AppID;
    private             $privateKey;

    private             $SignType       = 'sha1';
    private             $Version    = "1.0";

    public             $requestArray    = [];
    private             $Signed;

    /**
     * 初始化api客户端
     *
     */
    public function __construct(){

    }

    public function set_data($postBaseContent){
        try {
            if (!$postBaseContent) {
                $postBaseContent = file_get_contents('php://input');
            }
            try {
                $url_data = base64_decode($postBaseContent);
            } catch (Exception $e) {
                throw new UnprocessableEntityHttpException('Base64 编码出错。');
            }
            $real_data = urldecode($url_data);
            parse_str($real_data, $data_array);

            if (!is_array($data_array)) {
                throw new UnprocessableEntityHttpException('请求实体有误。');
            }

            if (!isset($data_array['Signed'])) {
                throw new UnprocessableEntityHttpException('请求实体有误。');
            }
            $signed = $data_array['Signed'];
            $this->Signed = $signed;
            unset($data_array['Signed']);
            $this->requestArray = $data_array;

            $this->init_data();
            $this->checkFields();
            $this->checkSigned();
            
        } catch (Exceptiopn $e) {
            Log::error($e->getMessage());
            throw new UnprocessableEntityHttpException('Unknow Error. Please try again. if error continue, please contact admin.');
        }        
    }

    public function init_data(){
        $this->AppID = $this->requestArray['AppID'];
        $row = AuthAccount::where(['app_id' => $this->AppID])->first();
        if ($row) {
            $this->privateKey = $row->app_screct;
        }else{
            throw new UnprocessableEntityHttpException('APP not found');
        }
    }

    public function checkSigned(){
        $content = $this->buildQuery($this->requestArray);
        $signed =  sha1(urlencode($content) . $this->privateKey);
        if ($signed != $this->Signed) {
            throw new UnprocessableEntityHttpException('签名验证失败.');
        }
    }

    public function checkFields(){

        $fields = array(
            "AppID",
            "SignType",
            "Version",
            "Timestamp",
        );
        foreach ($fields as $field) {
            if (!isset($this->requestArray[$field])) {
                throw new UnprocessableEntityHttpException('field: ' . $field . '是必需的。 ');
            }
        }
    }

    public function getUserKeys(){

        $fields = array(
            "AppID",
            "SignType",
            "Version",
            "Timestamp",
            "Data",
        );
        $userRequests = $this->requestArray;

        foreach ($fields as $field) {
            if (isset($userRequests[$field])) {
                unset($userRequests[$field]);
            }
        }
       return array_keys($userRequests);
    }

    /*
     * 查询参数排序 a-z
     * */
    public function buildQuery( $query ){
        if ( !$query ) {
            return null;
        }
        //将要 参数 排序
        ksort( $query );
        //重新组装参数
        $params = array();
        foreach($query as $key => $value){
            $params[] = $key .'='. $value ;
        }
        $data = implode('&', $params);
        return $data;
    }

    public function buildBody($args){
        $fields = array(
            "AppID",
            "SignType",
            "Version",
            "Timestamp",
        );
        foreach ($fields as $field) {
            if (!isset($args[$field])) {
                if ($field != 'Timestamp') {
                    $args[$field] = $this->$field;
                }else{
                    $args[$field] = date('Y-m-d H:i:s');
                }
            }
        }
        $content = $this->buildQuery($args);
        $uc = urlencode($content);
        $signed = sha1($uc . $this->privateKey);
        $args['Signed'] = $signed;
        $fc = $this->buildQuery($args);
        $ffc = base64_encode(urlencode($fc));
        return $ffc;
    }

    public function sendWechatTemplateMsg($openid){
        $weapp = app(\EasyWeChat\Foundation\Application::class);

        $notice = $weapp->notice;

        $userKeys = $this->getUserKeys();

        $payload = [
            'touser' => $this->requestArray['Touser'],
            'template_id' => $this->requestArray['Template_ID'],
            'url' => isset($requestArray['Url']) ? $requestArray['Url'] : '',
        ];
        foreach ($userKeys as $key) {
            $key_lower = strtolower($key);
            $payload[$key_lower] = $this->requestArray[$key];
        }

        $payload['data'] =  json_decode(base64_decode($this->requestArray['Data']),true);

        try {
            $return = $notice->send($payload);

            Log::info(sprintf("模板消息己发送， 消息ID: %s", $return));
            return json_decode($return, true);
        } catch (Exception $e) {
            Log::warning(sprintf("获取ECP(%s)信息失败！, error: %s. \n", $openid, $e->getMessage()));
            return ['errcode' => 500, 'errmsg' => '发送消息发败。' . $e->getMessage()];
        }

    }

}