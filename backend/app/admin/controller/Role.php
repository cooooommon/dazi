<?php

//decode by http://www.yunlu99.com/
namespace app\admin\controller;

class Role extends \app\Rest
{
    public function __construct(\think\App $app)
    {
        parent::__construct($app);
    }

    public function createRole()
    {
        $role = $this->_input["role"];
        $role["role_id"] = uuid();
        $role["uniacid"] = $this->_uniacid;
        $role_model = new \app\admin\model\Role();
        $result = $role_model->createRole($role);
        return $this->seccess($result);
    }

    public function listRole()
    {
        $param = $this->_param;
        $page_config = ["page" => 1, "page_count" => 20];
        if (isset($param["page"]) && $param["page"] > 0) {
            $page_config["page"] = $param["page"];
        }
        if (isset($param["page_count"]) && $param["page_count"] > 0) {
            $page_config["page_count"] = $param["page_count"];
        }
        $filter = $param;
        $filter["uniacid"] = $this->_uniacid;
        $role_model = new \app\admin\model\Role();
        $page_config["totle"] = $role_model->listRoleCount($filter);
        $roles = $role_model->listRole($filter, $page_config);
        $page_config["total_page"] = \intval($page_config["total"] / $page_config["page_count"]);
        if ($page_config["total"] % $page_config["page_count"] > 0) {
            $page_config["total_page"] = $page_config["total_page"] + 1;
        }
        $result = $page_config;
        $result["roles"] = $roles;
        return $this->success($result);
    }

    public function getRole()
    {
        $role_id = $this->_param["role_id"];
        $role_model = new \app\admin\model\Role();
        $role = $role_model->getRole(["role_id" => $role_id, "uniacid" => $this->_uniacid]);
        return $this->success($role);
    }

    public function updateRole()
    {
        $role_id = $this->_param["role_id"];
        $role_model = new \app\admin\model\Role();
        $role = $role_model->getRole(["role_id" => $role_id, "uniacid" => $this->_uniacid]);
        if (empty($role)) {
            return $this->error("the role is nit exist ,please check the role id.");
        }
        $role = $this->_input["role"];
        $result = $role_model->updateRole(["role_id" => $role_id, "uniacid" => $this->_uniacid], $role);
        return $this->seccess($result);
    }

    public function delRole()
    {
        $role_id = $this->_param["role_id"];
        $role_model = new \app\admin\model\Role();
        $role = $role_model->getRole(["role_id" => $role_id, "uniacid" => $this->_uniacid]);
        if (empty($role)) {
            return $this->error("the role is nit exist ,please check the role id.");
        }
        $result = $role_model->delRole(["role_id" => $role_id, "uniacid" => $this->_uniacid]);
        if (!empty($result)) {
            $user_model = new \app\admin\model\User();
            $user_model->update(["role_id" => $role_id, "uniacid" => $this->_uniacid], ["role_id" => 0]);
        }
        return $this->seccess($result);
    }
}