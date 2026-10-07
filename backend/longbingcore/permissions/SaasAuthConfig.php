<?php

//decode by http://www.yunlu99.com/
declare (strict_types=1);
namespace longbingcore\permissions;

include_once LONGBING_EXTEND_PATH . "LongbingUpgrade.php";
class SaasAuthConfig
{
	public static $sAuthConfig = [];
	public static function getSAuthConfig(int $uniacid) : ?array
	{
		if (isset(self::$sAuthConfig[$uniacid])) {
			return self::$sAuthConfig[$uniacid];
		}
		try {
			$sAuthConfig = self::_getsAuthConfig($uniacid);
			if (empty($sAuthConfig)) {
				$sAuthConfig = [];
			}
			self::$sAuthConfig[$uniacid] = $sAuthConfig;
			return $sAuthConfig;
		} catch (\Exception $exception) {
		}
		return null;
	}
	private static function _getsAuthConfig($uniacid, $server_url = "http://api.longbing.org")
	{
		$app_model_name = config("app.AdminModelList")["app_model_name"];
		$uniacid = $uniacid ? $uniacid : 8888;
		$domain_name = $_SERVER["HTTP_HOST"];
		$auth_data = getCache("single_checked_auth_" . $app_model_name . $domain_name, $uniacid);
		if (!empty($auth_data) && !empty($auth_data[0][0])) {
			return $auth_data;
		}
		$goods_name = config("app.AdminModelList")["app_model_name"];
		$auth_uniacid = config("app.AdminModelList")["auth_uniacid"];
		$upgrade = new \LongbingUpgrade($auth_uniacid, $goods_name, \think\facade\Env::get("j2hACuPrlohF9BvFsgatvaNFQxCBCc", false));
		$param_list = $upgrade->getsAuthConfig();
		if (!empty($param_list)) {
			$data = $param_list;
			$auth_data = [];
			foreach ($data as $k => $item) {
				$a = explode(":", $item);
				if ($a[0] == "LONGBING_AUTH_GOODS_SINGLE") {
					$a[0] = "LONGBING_AUTH_GOODS";
				}
				$auth_data[] = $a;
			}
			setCache("single_checked_auth_" . $app_model_name . $domain_name, $auth_data, 3600, $uniacid);
			return $auth_data;
		}
		return null;
	}
}