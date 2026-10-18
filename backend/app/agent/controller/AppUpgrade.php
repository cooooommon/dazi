<?php
declare (strict_types=1);

namespace app\agent\controller;

include_once LONGBING_EXTEND_PATH."LongbingUpgrade.php";

class AppUpgrade extends \app\AdminRest
{
    public function getUpgradeInfo()
    {
//		$goods_name = config("app.AdminModelList")["app_model_name"];
//		$auth_uniacid = config("app.AdminModelList")["auth_uniacid"];
//		$version_no = config("app.AdminModelList")["version_no"];
//		$upgrade = new \LongbingUpgrade($auth_uniacid, $goods_name, \think\facade\Env::get("j2hACuPrlohF9BvFsgatvaNFQxCBCc", false));
//		$data = $upgrade->checkAuth();
        $data["location_version_no"] = 1;
        $data["is_upgrade"] = false;
        $this->update();
        return $this->success($data);
    }

    public function update()
    {
        $key = "init_all_data";
        setCache($key, "", 7200, $this->_uniacid);
        \app\admin\service\UpdateService::installSql($this->_uniacid);
        return $this->success([]);
    }

    private function getIsUpgrade()
    {
        if (!longbingIsWeiqin()) {
            return true;
        } else {
            return false;
        }
    }

    public function upgrade()
    {
        //远程升级包没有可靠的签名校验，且会解压覆盖站点代码，禁止在线升级
        $this->errorMsg('出于安全考虑，已禁用在线升级，请手动部署更新包');
    }
}