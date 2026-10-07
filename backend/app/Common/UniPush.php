<?php
namespace app\Common;
use think\facade\Config;
require_once(APP_PATH .'extend/uniPush/igetui.php');
require_once(APP_PATH .'extend/uniPush/igetui/template/notify/IGt.Notify.php');


class UniPush
{
    private $cid;
    private $title = '';
    private $content = '';
    private $payload = '';
    private $package = '';//包名
    function __construct(){
        
    }
    
    // 返回错误信息
    function error($des){
        echo '!!ERROR!!'.PHP_EOL;
        echo $des;
        echo PHP_EOL;
    }    
    


}
