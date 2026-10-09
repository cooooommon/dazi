<?php

namespace app\index\controller;

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
        View::assign('jsPath', $jsPath);
        View::assign('is_founder', $is_founder);
        View::assign('isWe7', $is_we7);
        View::assign('app_css', $app_css.$timestamp);
        View::assign('manifest', $manifest.$timestamp);
        View::assign('vendor', $vendor.$timestamp);
        View::assign('app', $app.$timestamp);
        return View::fetch();
    }
}
