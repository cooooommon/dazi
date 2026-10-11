<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Env;

defined('IN_IA') || define('IN_IA', true);
$dir_path = __DIR__.'/../../../../data/config.php';
if (file_exists($dir_path)) {
    require $dir_path;
} else {
    $config = array();
    $config['db']['master']['host'] = Env::get('database.hostname', '127.0.0.1');
    $config['db']['master']['username'] = Env::get('database.username', 'root');
    $config['db']['master']['password'] = Env::get('database.password', '');
    $config['db']['master']['port'] = Env::get('database.hostport', '3323');
    $config['db']['master']['database'] = Env::get('database.database', '');
    $config['db']['master']['charset'] = Env::get('database.charset', 'utf8mb4');
    $config['db']['master']['pconnect'] = 0;
    $config['db']['master']['tablepre'] = Env::get('database.prefix', 'ims');
}
return [
    // 默认使用的数据库连接配置
    'default' => Env::get('database.driver', 'mysql'),
    // 数据库连接配置信息
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => $config['db']['master']['host'],
            // 数据库名
            'database' => $config['db']['master']['database'],
            // 用户名
            'username' => $config['db']['master']['username'],
            // 密码
            'password' => $config['db']['master']['password'],
            // 端口
            'hostport' => $config['db']['master']['port'],
            // 连接dsn
            'dsn' => '',
            // 数据库连接参数
            'params' => [
                // 与生产 MySQL 5.6 保持一致的宽松分组语义
                // MySQL 5.7+ 默认开启 ONLY_FULL_GROUP_BY，而应用内存在大量
                // field('a.*')->group('非主键') 的写法（会报 1055），此处按连接关闭该模式
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))",

                // 注意：这里【不要】设置 PDO::ATTR_EMULATE_PREPARES => true。
                // 它能让每条查询少 2 次往返（本地经 Docker 端口代理时单条 50ms -> 4ms），
                // 但会让所有数值列以【字符串】返回（status 由 int -1 变成 "-1"），
                // 而前后端有大量 `status === 1` 这类严格比较（后台 web/ 就有 285 处），
                // 会导致状态显示全错。生产环境应用与 MySQL 同机、原生预处理本来就快，
                // 开启模拟预处理是纯损害。如确实要优化本地查询延迟，请改从
                // "减少查询次数"（消除 N+1）或调整本地网络拓扑入手。
            ],
            // 数据库编码默认采用utf8
            'charset' => 'utf8mb4',
            // 数据库表前缀
            'prefix' => $config['db']['master']['tablepre'],
            // 数据库调试模式
            'debug' => Env::get('APP_DEBUG', false),
            // 监听SQL
            'trigger_sql' => Env::get('APP_DEBUG', false),
            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy' => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate' => false,
            // 读写分离后 主服务器数量
            'master_num' => 1,
            // 指定从服务器序号
            'slave_no' => '',
            // 是否严格检查字段是否存在
            'fields_strict' => true,
            // 是否需要进行SQL性能分析
            'sql_explain' => false,
            // Builder类
            'builder' => '',
            // Query类
            'query' => '',
            // 是否需要断线重连
            'break_reconnect' => false,
            //数据集返回类型
            'resultset_type' => 'array',
        ],

        // 更多的数据库配置信息
    ],

    // 自定义时间查询规则
    'time_query_rule' => [],
    // 自动写入时间戳字段
//    'auto_timestamp'  => 'timestamp',
    'auto_timestamp' => true,
    // 时间字段取出后的默认时间格式
//    'datetime_format' => 'Y-m-d H:i:s',
];
