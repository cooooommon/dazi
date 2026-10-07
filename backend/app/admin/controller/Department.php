<?php
namespace app\admin\controller;

class Department extends \app\Rest
{
    public function __construct(\think\App $app)
    {
        parent::__construct($app);
    }

    public function createDepartmet()
    {
        $department = $this->_input["department"];
        $department["department_id"] = uuid();
        if (isset($this->_user)) {
            $department["creator_id"] = $this->_user["user_id"];
        }
        $department_model = new \app\admin\model\Department();
        $result = $department_model->updateDepartment(["deparement_id" => $department_id, "uniacid" => $this->_uniacid],
            $department);
        return $this->success($result);
    }

    public function listDepartmet()
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
        $department_model = new \app\admin\model\Department();
        $page_config["total"] = $department_model->listDepartmentCount($filter);
        $departmets = $department_model->listDepartment($filter);
        $page_config["total_page"] = \intval($page_config["total"] / $page_config["page_count"]);
        if ($page_config["total"] % $page_config["page_count"] > 0) {
            $page_config["total_page"] = $page_config["total_page"] + 1;
        }
        $result = $page_config;
        $result["departments"] = $departmets;
        return $this->success($result);
    }

    public function getDepartmet()
    {
        $department_id = $this->_param["department_id"];
        $department_model = new \app\admin\model\Department();
        $department = $department_model->getDepartment([
            "deparement_id" => $department_id, "uniacid" => $this->_uniacid
        ]);
        if (!empty($department)) {
            $departments = $department_model->listDepartmentAll([
                "parent_id" => $department_id, "uniacid" => $this->_uniacid
            ]);
            if (!empty($departments)) {
                $department["departments"] = $departments;
            }
            $user_model = new \app\admin\model\User();
            $users = $user_model->listUserAll(["department_id" => $department_id, "uniacid" => $this->_uniacid]);
            if (!empty($users)) {
                $department["users"] = $users;
            }
        }
        return $this->success($department);
    }

    public function updateDepartmet()
    {
        $department_id = $this->_param["department_id"];
        $data = $this->_input["department"];
        $department_model = new \app\admin\model\Department();
        $department = $department_model->getDepartment([
            "deparement_id" => $department_id, "uniacid" => $this->_uniacid
        ]);
        if (empty($department)) {
            return $this->error("the department not exist ,please check department id .");
        }
        if (empty($data)) {
            return $this->error("the department change data is note exist ,please check department data.");
        }
        $result = $department_model->updateDepartment(["department_id" => $department_id, "uniacid" => $this->_uniacid],
            $data);
        return $this->success($result);
    }

    public function delDepartmet()
    {
        $department_id = $this->_param["department_id"];
        $deparmet_model = new \app\admin\model\Department();
        $department = $department_model->getDepartment([
            "deparement_id" => $department_id, "uniacid" => $this->_uniacid
        ]);
        if (empty($department)) {
            return $this->error("the department not exist ,please check department id .");
        }
        $result = $deparmet_model->delDepartment(["deparement_id" => $department_id, "uniacid" => $this->_uniacid],
            []);
        if (!empty($result)) {
            $user_model = new \app\admin\model\User();
            $user_model->updateUser(["uniacid" => $this->_uniacid, "department_id" => $department_id],
                ["department_id" => 0]);
        }
        return $this->success($result);
    }
}