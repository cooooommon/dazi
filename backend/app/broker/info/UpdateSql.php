<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/6
 * Time: 16:00
 * docs:
 */

//获取表前缀
$prefix = longbing_get_prefix();

//每个一个sql语句结束，都必须以英文分号结束。因为在执行sql时，需要分割单个脚本执行。
//表前缀需要自己添加{$prefix} 以下脚本被测试脚本


$sql = <<<updateSql

CREATE TABLE IF NOT EXISTS `{$prefix}massage_broker_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `name` varchar(32)  DEFAULT '',
  `mobile` varchar(32) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `update_time` bigint(11) DEFAULT '0',
  `text` varchar(625) DEFAULT '',
  `sh_text` varchar(625) DEFAULT '',
  `sh_time` bigint(11) DEFAULT '0',
  `balance` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='经纪人列表';

alter table `{$prefix}massage_service_coach_list` ADD COLUMN `broker_id` int(10) DEFAULT '0' COMMENT '经纪人id';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_id` int(10) DEFAULT '0' COMMENT '经纪人id';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_cash` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人佣金';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_balance` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人比例';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_coach_cash` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人向导承担佣金';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_coach_balance` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人向导承担比例';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_agent_cash` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人代理商承担佣金';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_agent_balance` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人代理商承担比例';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_admin_cash` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人平台承担佣金';
alter table `{$prefix}massage_service_order_list` ADD COLUMN `broker_admin_balance` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人平台承担佣金比例';

CREATE TABLE IF NOT EXISTS `{$prefix}massage_service_order_commission_share` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `comm_id` int(11) DEFAULT '0' COMMENT '佣金记录id',
  `share_balance` varchar(32) DEFAULT '0' COMMENT '比例',
  `share_cash` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `share_id` int(11) DEFAULT '0' COMMENT '被分摊人的id',
  `order_id` int(11) DEFAULT '0' COMMENT '订单id',
  `type` int(11) DEFAULT '0' COMMENT '1技师 2代理商',
  `cash_type` int(11) DEFAULT '1' COMMENT '1分摊',
  `comm_type` int(11) DEFAULT '0' COMMENT '佣金记录的type',
  PRIMARY KEY (`id`),
  KEY `comm_id` (`comm_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='佣金分摊表';

alter table `{$prefix}massage_broker_list` ADD COLUMN `total_cash` decimal(10,2) DEFAULT '0.00' COMMENT '总共佣金';
alter table `{$prefix}massage_broker_list` ADD COLUMN `cash` decimal(10,2) DEFAULT '0.00' COMMENT '可用佣金';



updateSql;

return $sql;
