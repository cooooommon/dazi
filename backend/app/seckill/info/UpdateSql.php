<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/9/19
 * Time: 14:35
 * docs:
 */

//获取表前缀
$prefix = longbing_get_prefix();

//每个一个sql语句结束，都必须以英文分号结束。因为在执行sql时，需要分割单个脚本执行。
//表前缀需要自己添加{$prefix} 以下脚本被测试脚本


$sql = <<<updateSql


updateSql;

return $sql;
