<?php

namespace app\index\controller;

use think\facade\Db;
use think\facade\View;

class Index
{
    public function index()
    {
        $timestamp = '?v='.time();
        $is_we7 = defined('IS_WEIQIN') ? true : false;
        $app_css = $is_we7 ? '/addons/'.APP_MODEL_NAME.'/core2/public/static/css/app.css' : '/static/css/app.css';
        $manifest = $is_we7 ? '/addons/'.APP_MODEL_NAME.'/core2/public/static/js/manifest.js' : '/static/js/manifest.js';
        $vendor = $is_we7 ? '/addons/'.APP_MODEL_NAME.'/core2/public/static/js/vendor.js' : '/static/js/vendor.js';
        $app = $is_we7 ? '/addons/'.APP_MODEL_NAME.'/core2/public/static/js/app.js' : '/static/js/app.js';
        $jsPath = $is_we7 ? '/addons/'.APP_MODEL_NAME.'/core2/public/' : '/';
        global $_W;
        $is_founder = $_W['isfounder'] ?? false;
        //静态资源版本号：取 app.js 的修改时间，每次重新部署 static 后自动变化（浏览器缓存自动失效）
        $v = time();
        try {
            $m = @filemtime(public_path().'static/js/app.js');
            if ($m) $v = $m;
        } catch (\Throwable $e) {}
        //管理后台 HTML 本身禁止缓存：防止部署后浏览器拿着旧 index.html 去找已删除的旧资源
        header('Cache-Control: no-store, must-revalidate');
        header('Pragma: no-cache');
        //腾讯地图key：优先读后台「系统设置-H5设置-腾讯地图key」
        //(shequshop_school_config.map_secret，与 common.php getLocationAddress、后台 H5设置表单同表同字段)；
        //未配置/异常时回退原内置 key，保证后台地图功能不中断
        $uniacid = intval(input('param.i', 666)) ?: 666;
        $map_secret = '';
        try {
            $config = (new \app\massage\model\Config())->dataInfo(['uniacid' => $uniacid]);
            $map_secret = !empty($config['map_secret']) ? (string)$config['map_secret'] : '';
        } catch (\Throwable $e) {
            $map_secret = '';
        }
        View::assign('jsPath', $jsPath);
        View::assign('is_founder', $is_founder);
        View::assign('isWe7', $is_we7);
        View::assign('app_css', $app_css.$timestamp);
        View::assign('manifest', $manifest.$timestamp);
        View::assign('vendor', $vendor.$timestamp);
        View::assign('app', $app.$timestamp);
        View::assign('map_secret', $map_secret ?: '2LNBZ-HT2R2-LENUE-CBNZK-QZ4VS-AQB5Z');
        View::assign('v', $v);
        return View::fetch();
    }
}
