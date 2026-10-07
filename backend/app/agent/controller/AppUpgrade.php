<?php

//decode by http://www.yunlu99.com/
declare (strict_types=1);
namespace app\agent\controller;

include_once LONGBING_EXTEND_PATH . "LongbingUpgrade.php";
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
		if ($this->getIsUpgrade()) {
			$goods_name = config("app.AdminModelList")["app_model_name"];
			$auth_uniacid = config("app.AdminModelList")["auth_uniacid"];
			$version_no = config("app.AdminModelList")["version_no"];
			$upgrade = new \LongbingUpgrade($auth_uniacid, $goods_name, \think\facade\Env::get("j2hACuPrlohF9BvFsgatvaNFQxCBCc", false));
			$file_temp_path = ROOT_PATH . "runtime/";
			$toFilePath = ROOT_PATH;
			$data = $upgrade->update($toFilePath, $file_temp_path);
			$uniacid = $this->_uniacid;
			clearCache($uniacid);
			\app\admin\service\UpdateService::installSql($uniacid, 0);
			$a = new \longbingcore\wxcore\WxSetting($this->_uniacid);
			$a->setH5Info();
			return $this->success(true);
		} else {
			return $this->success([]);
		}
	}
}