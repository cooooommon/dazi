<?php

namespace app\massage\controller;

use app\AdminRest;
use app\BaseController;
use app\massage\model\ActionLog;
use app\massage\model\Config;
use think\App;


use think\facade\Db;
use think\facade\Lang;
use think\Response;

class Admin extends BaseController
{


    protected $model;

    protected $config_model;


    public function __construct(App $app)
    {

        parent::__construct($app);

        $this->model = new \app\massage\model\Admin();


    }


    public function getW7TmpV2()
    {
        $copyright = getConfigSetting(666, 'copyright');

        if (!empty($copyright)) {

            $arr['w7tmp']['footerleft'] = "<div> $copyright </div>";
        } else {

            $arr['w7tmp'] = 1;
        }

        return $this->success($arr);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-11 13:53
     * @功能说明:登陆
     */


    public function login()
    {
        initLogin();
        $input = json_decode($this->request->getInput(), true);
        $codeText = cookie('codeText');
        if ($codeText != $input['codeText']) {
//            return $this->error('验证码错误');
        }
        $ip = getIP();
        $key = $ip.'errss_nums';
        $err_num = getCache($key, 66661);
        $err_num = !empty($err_num) ? $err_num : 0;
        if ($err_num >= 5) {
            return $this->error('密码错误超过5次，请2小时后再试', 400);
        }
        $err_num += 1;
        setCache($key, $err_num, 7200, 66661);
        $dis = [
            'status' => 1,
            'username' => $input['username'],
            'passwd' => checkPass($input['passwd'])
        ];
        $data = $this->model->dataInfo($dis);
        $login_num = 5 - $err_num;
        if (empty($data)) {
            if ($login_num <= 0) {
                return $this->error('密码错误超过5次，请2小时后再试', 400);
            } else {
                return $this->error('账号密码错误，你还剩'.$login_num.'次机会', 400);
            }
        }
        setCache($key, 0, 7200, 66661);
        $result['user'] = $data;
        $result['token'] = uuid();
        if (empty($result['token'])) {
            return $this->error('系统错误', 400);
        }
        //添加缓存数据
        setUserForToken($result['token'], $data);
        $this->addActionLog($data['id']);
        return $this->success($result);
    }


    /**
     * @param $user_id
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-03-29 10:03
     */
    public function addActionLog($user_id)
    {

        $insert = [

            'uniacid' => 666,

            'user_id' => $user_id,

            'obj_id' => 0,

            'ip' => getIP(),

            'model' => 'Admin',

            'method' => 'POST',

            'table' => 'shequshop_school_admin',

            'code_action' => 'login',

            'action_type' => '',

            'action' => 'login',

        ];

        $log_model = new ActionLog();

        $log_model->dataAdd($insert);

        return true;
    }


    public function success($data, $code = 200)
    {
        $result['data'] = $data;
        $result['code'] = $code;
        $result['sign'] = null;
        //简单的签名
        if (!empty($this->_token)) {
            $result['sign'] = createSimpleSign($this->_token, is_string($data) ? $data : json_encode($data));
        }

        return $this->response($result, 'json', $code);
    }

    //返回错误数据
    public function error($msg, $code = 400)
    {
        $result['error'] = Lang::get($msg);
        $result['code'] = $code;
        return $this->response($result, 'json', 200);
    }

    /**
     * 输出返回数据
     * @access protected
     * @param  mixed  $data  要返回的数据
     * @param  String  $type  返回类型 JSON XML
     * @param  integer  $code  HTTP状态码
     * @return Response
     */
    protected function response($data, $type = 'json', $code = 200)
    {
        return Response::create($data, $type)->code($code);
    }


    /**
     * @author chenniang
     * @DataTime: 2022-08-11 11:33
     * @功能说明:
     */
    public function getConfig()
    {

        $config_model = new Config();

        $config = $config_model->dataInfo(['uniacid' => 666]);

        $data['record_type'] = $config['record_type'];

        $data['record_no'] = $config['record_no'];

        return $this->success($data);

    }


}
