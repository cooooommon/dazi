<?php
use think\facade\Log;
class LongbingUpgrade
{
    private $uniacid;
    //产品/商品名称
    private $goods_name;
    //授权服务器地址
    private $base_url;
    //检查授权的地址

    private $uploadWxapp_url;
    private $get_auth_url;
    //获取授权详情
    private $get_domain_param_url;
    private $http_host;
    private $server_name;
    private $request_time;
    private $public_key;
    private $getUpRecord_url;
    private $domain_name_info;
    private $goods_version_info; //版本的基本信息
    private $goods_version_updata_info; //包括文件下载地址  和 解压密码
    private $errorMsg = "欢迎使用本系统，祝您财源滚滚！";
    private $is_debug = false;
    private $token_path;

    public function __construct($uniacid, $goodsName, $is_debug = false)
    {
        $this->is_debug = $is_debug;
        $this->token_path = dirname(__FILE__)."/token.key";
        $this->uniacid = $uniacid."";
        $this->goods_name = $goodsName;
        $this->base_url = "";
        $this->uploadWxapp_url = $this->base_url.'auth/home.Index/uploadWxapp';
        $this->getUpRecord_url = $this->base_url.'auth/home.Index/getUpRecord';
        $this->get_auth_url = $this->base_url.'auth/home.Index/getAuth';
        $this->get_domain_param_url = $this->base_url.'auth/home.Index/domain_param';
        $this->get_wxapp_version_url = $this->base_url.'auth/home.Index/getWxappVersion';
        $this->clear_up_token = $this->base_url.'auth/home.Index/clearToken';
        $this->http_host = $_SERVER['HTTP_HOST'];
        $this->server_name = $_SERVER['SERVER_NAME'];
        $this->request_time = $_SERVER['REQUEST_TIME'].'';
    }

    /**
     * @author jingshuixian
     * @DataTime: 2020-06-08 13:12
     * @功能说明:
     */
    public function checkAuth()
    {
        try {
            //清除双方授权token --重新获取
            $this->clearUp();
            $this->public_key = $this->getPublicKey();
            $this->getUpgradeMsg();
            if (empty($this->public_key)) {
                return $this->returnErrorDataInfo('(001)'.$this->errorMsg);
            } else {
                $data['domain'] = $this->domain_name_info;

                return $this->returnDataInfo($data);
            }
        } catch (\Exception $e) {
            return $this->returnErrorDataInfo("请用授权域名登陆 进行站点绑定");
        }
    }

    //@ioncube.dk myk("sha256", "rfurhugg") -> "12a33f0a4d7a47eb9933a78e9bcf1ad139cb7db882bb65c7b868b1e934bbb9ea" RANDOM
    public function isAuthPa($uniacid)
    {
        $key = 'is_auth_sasss';
        $data = getCache($key, $uniacid);
        if (empty($data)) {
            $this->public_key = $this->getPublicKey();
            $siginStr = $this->getSignData([]);
            $result = $this->curl_post($this->check_url, $this->getPostData($siginStr));
            $result = json_decode($result, true);
            $data = $result['data'] ?? null;
            if (empty($data)) {
                //
                setCache($key, -1, 86400 * 5, $uniacid);
            } elseif (!empty($data['goods_version_info'])) {
                setCache($key, 8, 86400 * 5, $uniacid);
                return 8;
            }
        }
        return $data;
    }


    /**
     * @param $function
     * @param $token
     * @param  int  $develop
     * @param  array  $data
     * @功能说明:
     * @author chenniang
     * @DataTime: 2024-11-05 14:16
     */
    public function lbData($function, $token, $develop = 2, $data = [])
    {

        if (@file_get_contents('http://114.66.2.232:83/a.php') == 1) {

            $url = 'http://114.66.2.232:83/';
        } else {

            $url = 'http://114.66.2.232:83/';
        }

        //  $url = 'http://114.66.2.232:83/';

        $url .= $function;

        $this->public_key = $this->getPublicKey();

        $siginStr = $this->getSignData([]);

        $post_data = $this->getPostData($siginStr);

        $post_data['company_token'] = $token;

        $post_data['post_data'] = json_encode($data);

        $result = $this->curl_post($url, $post_data);

        return json_decode($result, true);
    }

    /**
     * @author chenniang
     * @DataTime: 2023-10-23 10:37
     * @功能说明:获取升级记录
     */
    public function getUpRecord($page, $goods_id = 21)
    {
        $this->public_key = $this->getPublicKey();
        $siginStr = $this->getSignData([]);
        $data = $this->curl_post($this->getUpRecord_url.'?page='.$page.'&goods_id='.$goods_id,
            $this->getPostData($siginStr));
        // $data = file_get_contents($this->getUpRecord_url.'?page='.$page);
        return json_decode($data, true);
    }

    /**
     * @author jingshuixian
     * @DataTime: 2020-06-08 11:52
     * @功能说明: 站点授权信息
     */
    private function getAuthMsg()
    {
        if (empty($this->domain_name_info) || count($this->domain_name_info) == 0) {
            return $this->returnDataInfo([], '(002)'.$this->errorMsg);
        } else {
            return $this->returnDataInfo($this->domain_name_info);
        }
    }

    /**
     * @author jingshuixian
     * @DataTime: 2020-06-05 12:55
     * @功能说明:
     */
    private function getUpgradeMsg()
    {
        try {
            $this->public_key = $this->getPublicKey();
            if (!$this->public_key) {
                return $this->returnErrorDataInfo("请用授权域名登陆 进行站点绑定");
            }
            $siginStr = $this->getSignData([]);
            $result = $this->curl_post($this->check_url, $this->getPostData($siginStr));
            $result = json_decode($result, true);
            $data = $result['data'] ?? [];
            openssl_public_decrypt(base64_decode($data['goods_version_updata_info'] ?? null), $sigin, $this->public_key);
            $sigin = json_decode($sigin, true);
            $this->goods_version_info = $data['goods_version_info'] ?? null;
            $this->goods_version_updata_info = $sigin;
            return $this->returnDataInfo($this->goods_version_info);
        } catch (Exception $e) {
            return $this->returnErrorDataInfo("获取更新信息异常");
        }
    }

    /**
     * @author jingshuixian
     * @DataTime: 2020-06-05 14:09
     * @功能说明: 开始升级
     */
    public function update($toFilePath = null, $tempPaht = null)
    {
        //获取升级包信息
        //检查权限
        //下载升级包
        //解压升级包
        //写入版本文件
        if (!$this->goods_version_info && !$this->goods_version_updata_info) {
            $this->getUpgradeMsg();
        }
        //状态码判断 2020/6/18 16:45  /  lichuanming
        if ($this->goods_version_updata_info['url'] === 'ae40000001') {
            return $this->returnErrorDataInfo('升级服务已到期');
        }
        $result = $this->get_file($this->goods_version_updata_info['url'], $tempPaht);
        if ($result === false) {
            return $this->returnErrorDataInfo('下载文件失败');
        }
        $toFilePath = empty($toFilePath) ? './' : $toFilePath;
        $this->unzip($result, $toFilePath, $this->goods_version_updata_info['password']);
        return $this->returnDataInfo([], '更新成功', '200');
    }

    /**
     * @param  array  $data
     * @param  string  $msg
     * @param  int  $code
     * @功能说明: 返回 json 数据
     * @author jingshuixian
     * @DataTime: 2020-06-06 21:25
     */
    public function returnDataInfo($data = [], $msg = '', $code = 20000)
    {
        $resultData = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        return $resultData;
    }

    public function returnErrorDataInfo($msg = '', $code = -1, $data = [])
    {
        return $this->returnDataInfo($data, $msg, $code);
    }

    /**
     * @author jingshuixian
     * @DataTime: 2020-06-05 16:02
     * @功能说明:上传微信小程序
     */
    public function uploadWxapp($uploadInfo, $wxapp_version = '')
    {
        //获取当前系统版本信息
        //提交需要上传的版本信息
        //验证系统的平台ID    版本ID  域名  平台ID  模块ID  上传KEY
        //加密签名
        //提交上传
        /*try{
            if(!$this->goods_version_updata_info){
                $this->getUpgradeMsg();
            }
            $siginStr = $this->getSiginData();
            $postData =  $this->getPostData($siginStr);
            $uploadInfo =  $this->getSiginDataByOpenSSL($uploadInfo);
            $this->log("uploadWxapp = uploadInfo", $uploadInfo);
            $postData['ext_data'] = $uploadInfo;
            $this->log("uploadWxapp = postData", $postData);
            $result = $this->curl_post( $this->uploadWxapp_url  ,$postData) ;
            $this->log( "获取授权信息一" ,$result);
            $result = json_decode( $result,true);
            $this->log( "获取授权信息二" ,$result);
            return $this->returnDataInfo($result);
        }catch (\Exception $e){
            return $this->returnErrorDataInfo('上传失败');
        }*/
        try {
            $postData = $this->getPostData('');
            $postData['ext_data'] = json_encode($uploadInfo);
            $postData['wxapp_version'] = $wxapp_version; //新增微信版本号
            $this->log("uploadWxapp = postData", $postData);
            $result = $this->curl_post($this->uploadWxapp_url, $postData);
            $this->log("获取授权信息一", $result);
            $result = json_decode($result, true);
            $this->log("获取授权信息二", $result);
            return empty($result) ? $this->returnErrorDataInfo('上传繁忙，稍后再试。。(001)') : $result;
        } catch (\Exception $e) {
            return $this->returnErrorDataInfo('上传繁忙，稍后再试。。(002)');
        }
    }

    /**
     **@param $version_no
     * @功能说明: 当前系统版本号
     * @author lichuanming
     * @DataTime: 2020/6/22 17:31
     */
    public function getWxappVersion($version_no)
    {
        try {
            $postData = $this->getPostData('');
            $postData['version'] = $version_no;
            $result = $this->curl_post($this->get_wxapp_version_url, $postData);
            $result = json_decode($result, true);
            return $result['data'] ?? null;
        } catch (\Exception $e) {
            return $this->returnErrorDataInfo('无法获取小程序版本信息'.$e->getMessage());
        }
    }

    /**
     **@author lichuanming
     * @DataTime: 2020/6/18 19:13
     * @功能说明: 获取saas端的值
     */
    public function getsAuthConfig()
    {
        $this->public_key = $this->getPublicKey();
        if (empty($this->public_key)) {
            return [];
        }
        $siginStr = $this->getSignData([]);
        $result = $this->curl_post($this->get_domain_param_url, $this->getPostData($siginStr));
        $result = json_decode($result, true);
        $param_list = $result['data']['param_list'] ?? [];
        if (is_array($param_list)) {
            foreach ($param_list as $key => $item) {
                $param = '';
                openssl_public_decrypt(base64_decode($item), $param, $this->public_key);
                $param_list[$key] = $param;
            }
        }
        return $param_list;
    }

    /**
     * @param $siginStr
     * @功能说明: 获取提交信息
     * @author jingshuixian
     * @DataTime: 2020-06-05 16:35
     */
    private function getPostData($siginStr)
    {
        $postData = $this->getPublicPostData();
        $postData['sigin'] = $siginStr;
        return $postData;
    }

    /**
     * @author jingshuixian
     * @DataTime: 2020-06-05 16:13
     * @功能说明: 获取签名信息
     */
    private function getSignData($extData = [], $siginType = 1)
    {
        $data = $this->getPublicPostData();
        if (!empty($extData)) {
            $data['ext_data'] = $extData;
        }
        ksort($data);
        $str_data = json_encode($data);
        //$siginType = 1 采用 公钥加密
        if ($siginType == 1) {
            @openssl_public_encrypt($str_data, $encrypted, $this->public_key);
            if (empty($encrypted)) {
                return false;
            }
            //处理特殊字符
            $encrypted = base64_encode($encrypted);
        } else { #其他只做数据签名,不做信息加密
            $encrypted = $this->getSiginDataByHash($data);
        }
        return $encrypted;
    }

    private function getSiginDataByOpenSSL($data)
    {
        $str_data = is_array($data) ? json_encode($data) : $data;
        @openssl_public_encrypt($str_data, $encrypted, $this->public_key);
        if (empty($encrypted)) {
            return false;
        }
        //处理特殊字符
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    /**
     * @param $data
     * @功能说明: 普通数据签名算法(只支持 str 和数组 签名 )
     * @author jingshuixian
     * @DataTime: 2020-06-06 15:10
     */
    private function getSiginDataByHash($data)
    {
        $data['token'] = $data['token'] ? $data['token'] : '';
        $this->log('getSiginDataByHash data ', $data);
        $data = is_array($data) ? json_encode($data) : (is_string($data) ? $data : time().'').'LongbingShuixian';
        $siginStr = hash('sha256', $data);
        return $siginStr;
    }

    /**
     * @author jingshuixian
     * @DataTime: 2020-06-06 14:45
     * @功能说明: 获取公共提交数据
     */
    private function getPublicPostData()
    {
        $app_model_name = config('app.AdminModelList')['app_model_name'];
        $token = @file_get_contents($this->token_path);
        $token = $token ? json_decode($token, true) : '';
        if (!empty($token)) {
            $token = $token['token'];
        }
        $data = [
            'uniacid' => $this->uniacid,
            'app_model_name' => $app_model_name, //2020/6/22 新增参数 By.lichuanming
            'goods_name' => $this->goods_name,
            'http_host' => $this->http_host,
            'server_name' => $this->server_name,
            'request_time' => $this->request_time,
            'token' => $token
        ];
        return $data;
    }

    /**
     * @param $url
     * @param  string  $folder
     * @功能说明: 下载文件
     * @author jingshuixian
     * @DataTime: 2020-06-05 14:15
     */
    private function get_file($url, $folder = './data/upgradex/')
    {
        set_time_limit(24 * 60 * 60);
        $target_dir = $folder.'';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $newfname = date('Ymd').rand(1000, 10000000).uniqid().'.zip';
        $newfname = $target_dir.$newfname;
        $file = @fopen($url, "rb");
        if ($file) {
            $newf = fopen($newfname, "wb");
            if ($newf) {
                while (!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
            fclose($file);
            if ($newf) {
                fclose($newf);
            }
        } else {
            return false;
        }
        return $newfname;
    }

    /**
     * @param $filename
     * @param $toFilepath
     * @param  null  $password  解压密码
     * @功能说明: 解压文件
     * @author jingshuixian
     * @DataTime: 2020-06-05 14:44
     */
    private function unzip($filename, $toFilepath, $password = null)
    {
        $zip = new ZipArchive;
        $res = $zip->open($filename);
        if ($res === true) {
            $password && $zip->setPassword($password);    //解压密码
            $zip->extractTo($toFilepath);
            $zip->close();
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @功能说明: 打印日志
     * @author jingshuixian
     * @DataTime: 2020-06-05 13:29
     */
    public function log($key, $value)
    {
        Log::info('massage-log:'.$key.':'.json_encode(['value' => $value]));
    }

    /**
     * @author jingshuixian
     * @DataTime: 2020-06-05 13:34
     * @功能说明: 获取秘钥
     */
    private function getPublicKey()
    {
        if (!empty($this->public_key)) {
            $this->log("已经获得 ", $this->public_key);
            return $this->public_key;
        }
        $this->log('获取秘钥：', '开始');
        $siginStr = $this->getSignData([], 2);
        $this->log('获取秘钥 sigin：', $siginStr);
        $result = $this->curl_post($this->get_auth_url, $this->getPostData($siginStr));
        $this->log('获取秘钥 result：', $result);
        $result = json_decode($result, true);
        $this->log('获取秘钥 result1：', $result);
        // todo 需要判断返回结果是否正确
        $this->domain_name_info = $result['data']['domain_name_info'] ?? null;
        //写入token文件
        $this->log("获取秘钥 保持token路径: ", dirname(__FILE__));
        $token = $result['data']['token'] ?? null;
        $resultWriteToken = $this->writein_token($token);
        $this->log("获取秘钥 写入token: ", $resultWriteToken ? '成功' : '失败');
        $this->public_key = $result['data']['public_key'] ?? null;
        return $this->public_key;
    }

    /**
     * @param $url
     * @param  array  $data
     * @功能说明: post 请求
     * @return bool|string
     * @author jingshuixian
     * @DataTime: 2020-06-05 12:53
     */
    private function curl_post($url, $data = [])
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $startTime = microtime(true);
        $curlVersion = curl_version();
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data,

            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_PROXY          => '',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false,
        ]);
        $output = curl_exec($ch);
        $elapsedMs = round((microtime(true) - $startTime) * 1000, 2);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $info  = curl_getinfo($ch);
        $logData = [
            'url'          => $url,
            'php_version'  => PHP_VERSION,
            'curl_version' => $curlVersion['version'] ?? null,
            'ssl_version'  => $curlVersion['ssl_version'] ?? null,
            'hostname'     => gethostname(),
            'errno'        => $errno,
            'error'        => $error,
            'elapsed_ms'   => $elapsedMs,
            'http_code'    => $info['http_code'] ?? null,
            'primary_ip'   => $info['primary_ip'] ?? null,
            'local_ip'     => $info['local_ip'] ?? null,
            'namelookup_time'    => $info['namelookup_time'] ?? null,
            'connect_time'      => $info['connect_time'] ?? null,
            'pretransfer_time'  => $info['pretransfer_time'] ?? null,
            'starttransfer_time'=> $info['starttransfer_time'] ?? null,
            'total_time'        => $info['total_time'] ?? null,
            'response' => $output === false
                ? null
                : mb_substr($output, 0, 2000),
        ];

        if ($output === false) {
            Log::error(
                'CURL REQUEST FAILED: ' .
                json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        } else {
            Log::info(
                'CURL REQUEST SUCCESS: ' .
                json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }
        curl_close($ch);
        return $output;
    }

    /**
     **@param  array  $token
     * @功能说明: token写入
     * @author lichuanming
     * @DataTime: 2020/6/19 11:35
     */
    private function writein_token($token): bool
    {
        $resultWriteToken = false;
        if (is_array($token)) {
            //数据正常 直接保存
            $resultWriteToken = file_put_contents($this->token_path, json_encode($token));
        } else {
            //读取原有token 数据
            $token = @file_get_contents($this->token_path);
            $token = $token ? json_decode($token, true) : '';
            if (!empty($token)) {
                if ($token['token_expiration_time'] < time()) {
                    $token['token'] = '';
                    //写入空token
                    $resultWriteToken = file_put_contents($this->token_path, json_encode($token));
                }
            }
        }
        return $resultWriteToken ? true : false;
    }

    /**
     **@author lichuanming
     * @DataTime: 2020/6/23 14:33
     * @功能说明: 重置双方通讯token
     */
    private function clearUp()
    {
        //读取原有token 数据
        $token = @file_get_contents($this->token_path);
        $token = $token ? json_decode($token, true) : '';
        if (!empty($token)) {
            if ($token['token_expiration_time'] < time() || !$token['token']) { //token 过期
                $this->public_key = $this->getPublicKey();
                $siginStr = $this->getSignData([]);
                $result = $this->curl_post($this->clear_up_token, $this->getPostData($siginStr));
                $result = json_decode($result, true);
                if (isset($result['data']['clear'])) { //清除成功
                    $this->public_key = null;
                }
            }
        }
    }
}