<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class User extends \app\Rest
{
    public function __construct(\think\App $app)
    {
        parent::__construct($app);
    }

    public function createUser()
    {
        $user = $this->_input["user"];
        $data = checkAccountIsExist($user["account"], $this->_uniacid);
        if ($data) {
            return $this->error("account is exist ,please check again.");
        }
        $user_id = uuid();
        $user["user_id"] = $user_id;
        $user["offset"] = createOffset();
        $user["passwd"] = createPasswd($user["passwd"], $user["offset"]);
        $user["uniacid"] = $this->_uniacid;
        if (!isset($user["role_id"]) || !ckeckRole($user["role_id"], $this->_uniacid)) {
            $user["role_id"] = getRole()["role_id"];
        }
        if (!empty($this->_user)) {
            $user["creator_id"] = $this->_user["user_id"];
        }
        $user_model = new \app\admin\model\Admin();
        $result = $user_model->createUser($user);
        return $this->success($result);
    }

    public function listUser()
    {
        $param = $this->_param;
        $page_config = ["page" => 1, "page_count" => 20];
        if (isset($param["page"]) && $param["page"] > 0) {
            $page_config["page"] = $param["page"];
        }
        if (isset($param["page_count"]) && $param["page_count"] > 0) {
            $page_config["page_count"] = $param["page_count"];
        }
        $param["uniacid"] = $this->_uniacid;
        $filter = listUserFilter($param);
        $user_model = new \app\admin\model\Admin();
        $page_config["total"] = $user_model->listUserCount($filter);
        $users = $user_model->listUser($filter, $page_config);
        $page_config["total_page"] = \intval($page_config["total"] / $page_config["page_count"]);
        if ($page_config["total"] % $page_config["page_count"] > 0) {
            $page_config["total_page"] = $page_config["total_page"] + 1;
        }
        $result = $page_config;
        $result["users"] = $users;
        return $this->success($result);
    }

    public function getUser()
    {
        $user_id = $this->_param["user_id"];
        $user_model = new \app\admin\model\Admin();
        $user = $user_model->getUser(["user_id" => $user_id, "uniacid" => $this->_uniacid]);
        unset($user["passwd"]);
        unset($user["offset"]);
        return $this->success($user);
    }

    public function updateUser()
    {
        $user_id = $this->_param["user_id"];
        $user_model = new \app\admin\model\Admin();
        $user = $user_model->getUser(["user_id" => $user_id, "uniacid" => $this->_uniacid]);
        if (empty($user)) {
            return $this->error("the user not is exist ,please check user id.");
        }
        $user_data = getUpdateUserFilter($this->_input["user"]);
        if (isset($user_data["passwd"])) {
            if (!isset($user["offset"])) {
                $user["offset"] = createOffset();
            }
            $user_data["offset"] = $user["offset"];
            $user_data["passwd"] = createPasswd($user_data["passwd"], $user["offset"]);
        }
        $result = $user_model->updateUser(["user_id" => $user_id, "uniacid" => $this->_uniacid], $user_data);
        return $this->success($result);
    }

    public function delUser()
    {
        $user_id = $this->_param["user_id"];
        $user_model = new \app\admin\model\Admin();
        $user = $user_model->getUser(["user_id" => $user_id, "uniacid" => $this->_uniacid]);
        if (empty($user)) {
            return $this->error("the user not is exist ,please check user id.");
        }
        $result = $user_model->delUser(["user_id" => $user_id, "uniacid" => $this->_uniacid], ["deleted" => 0]);
        $admin_role_model = new \app\admin\model\AdminRole();
        $admin_role_model->delUserRole(["user_id" => $user_id]);
        return $this->success($result);
    }

    public function setUserRole()
    {
        $user_id = $this->_param["user_id"];
        $role_id = $this->_param["role_id"];
        $user_model = new \app\admin\model\Admin();
        $user = $user_model->getUser(["user_id" => $user_id, "uniacid" => $this->_uniacid]);
        if (empty($user)) {
            return $this->error("the user is not exist ,please check user id.");
        }
        $role = ckeckRole($role_id);
        if (empty($role)) {
            return $this->error("the role is not exist ,please check role id.");
        }
        $exist_role_ids = [];
        foreach ($user["role"] as $role) {
            $exist_role_ids[] = $role["role_ids"];
        }
        if (in_array($role_id, $exist_role_ids)) {
            return $this->error("the user had the role ,please do not repeat add role to the user.");
        }
        $user_role_model = UserRoleModel();
        $result = $user_role_model->createUserRole([
            "user_id" => $user_id, $role_id => $role_id, "uniacid" => $this->_uniacid
        ]);
        return $this->success($result);
    }

    public function removeUserRole()
    {
        $user_id = $this->_param["user_id"];
        $role_id = $this->_param["role_id"];
        $user_model = new \app\admin\model\Admin();
        $user = $user_model->getUser(["user_id" => $user_id, "uniacid" => $this->_uniacid]);
        if (empty($user)) {
            return $this->error("the user is not exist ,please check user id.");
        }
        $exist_role_ids = [];
        foreach ($user["role"] as $role) {
            $exist_role_ids[] = $role["role_ids"];
        }
        if (!in_array($role_id, $exist_role_ids)) {
            return $this->error("the user role is not exist ,please check role id.");
        }
        $user_role_model = UserRoleModel();
        $result = $user_role_model->delUserRole([
            "user_id" => $user_id, $role_id => $role_id, "uniacid" => $this->_uniacid
        ]);
        return $this->success($result);
    }
}