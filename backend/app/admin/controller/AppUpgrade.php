<?php

//decode by http://www.yunlu99.com/
declare (strict_types=1);

namespace app\admin\controller;

include_once LONGBING_EXTEND_PATH."LongbingUpgrade.php";

class AppUpgrade extends \app\AdminRest
{
    public function uploadWxapp()
    {
        $wxapp_version = isset($this->_param["wxapp_version"]) ? $this->_param["wxapp_version"] : "";
        $model = new \app\admin\model\WxUpload();
        $goods_name = config("app.AdminModelList")["app_model_name"];
        $auth_uniacid = config("app.AdminModelList")["auth_uniacid"];
        $version_no = config("app.AdminModelList")["version_no"];
        $upgrade = new \LongbingUpgrade($auth_uniacid, $goods_name,
            \think\facade\Env::get("j2hACuPrlohF9BvFsgatvaNFQxCBCc", false));
        $upload_config = $model->settingInfo(["uniacid" => $this->_uniacid]);
        if (empty($upload_config["key"])) {
            $this->errorMsg("还未填写密钥");
        }
        $upload_key_url = $upload_config["key"];
        $config_model = new \app\massage\model\Config();
        $app_id = $config_model->where(["uniacid" => $this->_uniacid])->value("appid");
        if (empty($app_id)) {
            $this->errorMsg("小程序未配置");
        }
        $uploadInfo = [
            "siteinfo" => [
                "uniacid" => $this->_uniacid, "multiid" => "0", "version" => "3.0",
                "siteroot" => $this->_is_weiqin ? "https://".$_SERVER["HTTP_HOST"]."/app/index.php" : "https://".$_SERVER["HTTP_HOST"]."/index.php"
            ], "upload_key" => "http://".$_SERVER["HTTP_HOST"]."/attachment/".$upload_key_url,
            "version_no" => $version_no, "version" => $upload_config["version"], "content" => $upload_config["content"],
            "app_id" => $app_id, "app_id_list" => explode(",", $upload_config["app_id"])
        ];
        $data = $upgrade->uploadWxapp($uploadInfo, $wxapp_version);
        return $this->success($data);
    }

    public function getWxappVersion()
    {
        $goods_name = config("app.AdminModelList")["app_model_name"];
        $auth_uniacid = config("app.AdminModelList")["auth_uniacid"];
        $version_no = config("app.AdminModelList")["version_no"];
        $upgrade = new \LongbingUpgrade($auth_uniacid, $goods_name,
            \think\facade\Env::get("j2hACuPrlohF9BvFsgatvaNFQxCBCc", false));
        $version = $upgrade->getWxappVersion($version_no);
        return $this->success($version);
    }
}