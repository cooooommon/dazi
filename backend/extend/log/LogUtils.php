<?php
namespace log;
class LogUtils
{
    /**
     * 写日志
     * @param $msg string 日志内容
     * @param $_path string 存放路径
     * @param $log_name string 日志名称
     * @return mixed
     */
    public static function log($msg, $_path = '', $log_name = '')
    {
//        $log_name = iconv('UTF-8', 'GBK', $log_name);
        $dir = substr(dirname(__DIR__), 0, -6);
        $path = $dir . 'public/logs/';
        if ($_path) {
            $path .= $_path . '/' . date('Y-m-d') . '/';
        }
        //判断路径是否存在
        create_dir($path);
        $fp = fopen($path . $log_name . date('Ymd') . ".txt", "a");
        flock($fp, LOCK_EX);
        fwrite($fp, "执行日期：" . strftime("%Y%m%d%H%M%S", time()) . "\n" . '$path=' . $path . "\n" . $msg . "\n" . "\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
