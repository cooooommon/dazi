<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class Auth extends \app\AdminRest
{
    public function __construct(\think\App $app)
    {
        parent::__construct($app);
    }

    public function auth()
    {
        $filter = $this->_input["user"];
        $admin_model = new \app\admin\model\Admin();
        $admin = $admin_model->getAdmin(["account" => $filter["account"], "uniacid" => $this->_uniacid]);
        if (empty($admin)) {
            return $this->error("account is not exist ,please check user account.");
        }
        if (!checkPasswd($filter["passwd"], $admin["offset"], $admin["passwd"])) {
            return $this->error("passwd is error ,please check user passwd.");
        }
        unset($admin["passwd"]);
        unset($admin["offset"]);
        $result["user"] = $admin;
        $result["token"] = createToken();
        if (empty($result["token"])) {
            return $this->error("System is busy,please try again later.");
        }
        setUserForToken($result["token"], $admin);
        return $this->success($result);
    }

    public function unAuth()
    {
        if (empty($this->_user)) {
            return $this->error("The user is not logged in.");
        }
        delUserForToken($this->_token);
        return $this->success(true);
    }
}