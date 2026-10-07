-- MySQL dump 10.13  Distrib 5.6.50, for Linux (x86_64)
--
-- Host: localhost    Database: yigoym
-- ------------------------------------------------------
-- Server version	5.6.50-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ims_massage_action_diy`
--

DROP TABLE IF EXISTS `ims_massage_action_diy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_action_diy` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `diy_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'diy名称',
  `page` mediumtext NOT NULL COMMENT 'diy页面内容',
  `tabbar` text NOT NULL COMMENT 'tabbar配置',
  `status` int(3) NOT NULL DEFAULT '1' COMMENT '1 启用  0未启用',
  `uniacid` int(10) NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `is_system` int(3) NOT NULL DEFAULT '0' COMMENT '是否是系统默认',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='diy表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_action_diy`
--

LOCK TABLES `ims_massage_action_diy` WRITE;
/*!40000 ALTER TABLE `ims_massage_action_diy` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_action_diy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_action_log`
--

DROP TABLE IF EXISTS `ims_massage_action_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_action_log` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `ip` varchar(32) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `model` varchar(32) DEFAULT '' COMMENT '模块名',
  `action` varchar(32) DEFAULT '',
  `obj_id` varchar(64) DEFAULT '0',
  `code_action` varchar(255) DEFAULT '',
  `table` varchar(255) DEFAULT '',
  `method` varchar(32) DEFAULT '',
  `action_type` varchar(64) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `data` mediumtext COMMENT '提交的数据',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `uniacid` (`uniacid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4601 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_action_log`
--

LOCK TABLES `ims_massage_action_log` WRITE;
/*!40000 ALTER TABLE `ims_massage_action_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_action_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_add_clock_setting`
--

DROP TABLE IF EXISTS `ims_massage_add_clock_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_add_clock_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `times` int(11) DEFAULT '1',
  `balance` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_add_clock_setting`
--

LOCK TABLES `ims_massage_add_clock_setting` WRITE;
/*!40000 ALTER TABLE `ims_massage_add_clock_setting` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_add_clock_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_aliyun_phone_config`
--

DROP TABLE IF EXISTS `ims_massage_aliyun_phone_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_aliyun_phone_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `pool_key` varchar(128) DEFAULT '' COMMENT '号码池',
  `virtual_status` tinyint(3) DEFAULT '0',
  `reminder_public` tinyint(3) DEFAULT '0' COMMENT '语音通知 专属模式 1公共模式',
  `reminder_tmpl_id` varchar(64) DEFAULT '' COMMENT '语音通知模版id',
  `reminder_phone` varchar(32) DEFAULT '' COMMENT '语音通知电话',
  `reminder_status` tinyint(3) DEFAULT '0' COMMENT '语音通知开关',
  `reminder_timing` int(255) DEFAULT '0' COMMENT '语音通知定时任务',
  `reminder_admin_status` tinyint(3) DEFAULT '0' COMMENT '来电提醒是否通知管理员',
  `reminder_admin_phone` text,
  `notice_agent` tinyint(3) DEFAULT '0' COMMENT '是否通知代理商',
  `moor_phone_arr` varchar(1024) DEFAULT '' COMMENT '七莫号码',
  `moor_id` varchar(64) DEFAULT '',
  `moor_url` varchar(255) DEFAULT 'https://openapis.7moor.com',
  `moor_secret` varchar(64) DEFAULT '',
  `virtual_type` tinyint(3) DEFAULT '1',
  `reminder_type` tinyint(3) DEFAULT '1' COMMENT '语音通知 type1 阿里云 2七莫',
  `reminder_text` varchar(1024) DEFAULT '技师您好，您有新的订单注意查看' COMMENT '语音通知内容',
  `moor_reminder_phone` varchar(32) DEFAULT '' COMMENT '七莫语音通知',
  `winnerlook_appid` varchar(128) DEFAULT '' COMMENT '云信appid',
  `winnerlook_token` varchar(128) DEFAULT '' COMMENT '云信token',
  `winnerlook_phone_arr` varchar(1024) DEFAULT '',
  `moor_virtual_type` tinyint(3) DEFAULT '1' COMMENT '七陌虚拟号模式',
  `help_tmpl_id` varchar(64) DEFAULT '' COMMENT '求救通知模版id',
  `help_tmpl_text` varchar(255) DEFAULT '你有技师正在发出求救' COMMENT '求救通知模版内容',
  `notice_admin` tinyint(3) DEFAULT '0' COMMENT '有代理商的技师 都不通知平台',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_aliyun_phone_config`
--

LOCK TABLES `ims_massage_aliyun_phone_config` WRITE;
/*!40000 ALTER TABLE `ims_massage_aliyun_phone_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_aliyun_phone_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_aliyun_phone_record`
--

DROP TABLE IF EXISTS `ims_massage_aliyun_phone_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_aliyun_phone_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `subs_id` varchar(64) DEFAULT '',
  `phone_a` varchar(32) DEFAULT '',
  `phone_b` varchar(32) DEFAULT '',
  `phone_x` varchar(32) DEFAULT '',
  `expire_date` bigint(11) DEFAULT '0',
  `status` bigint(11) DEFAULT '1',
  `need_record` tinyint(3) DEFAULT '1',
  `pool_key` varchar(255) DEFAULT '',
  `order_id` int(11) DEFAULT '0',
  `order_code` varchar(255) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `text` text,
  `type` tinyint(3) DEFAULT '1',
  `order_type` tinyint(3) DEFAULT '1' COMMENT '订单类型1服务订单 2邀约订单',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_aliyun_phone_record`
--

LOCK TABLES `ims_massage_aliyun_phone_record` WRITE;
/*!40000 ALTER TABLE `ims_massage_aliyun_phone_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_aliyun_phone_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_aliyun_play_record`
--

DROP TABLE IF EXISTS `ims_massage_aliyun_play_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_aliyun_play_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `pool_key` varchar(128) DEFAULT '',
  `phone_x` varchar(32) DEFAULT '' COMMENT '虚拟号码',
  `phone_a` varchar(32) DEFAULT '',
  `phone_b` varchar(32) DEFAULT '',
  `call_time` bigint(11) DEFAULT NULL COMMENT '拨打时间',
  `start_time` bigint(11) DEFAULT '0' COMMENT '接起时间',
  `end_time` bigint(11) DEFAULT '0' COMMENT '挂断时间',
  `record_url` varchar(255) DEFAULT NULL COMMENT '放音录音URL',
  `ring_record_url` varchar(255) DEFAULT NULL COMMENT '放音录音URL',
  `call_id` varchar(64) DEFAULT '' COMMENT '话记录ID',
  `sub_id` varchar(64) DEFAULT '' COMMENT '关系',
  `out_id` varchar(128) DEFAULT '',
  `call_type` int(11) DEFAULT '0' COMMENT '呼叫类型。取值：0：主叫，即phone_no打给peer_no。1：被叫，即peer_no打给phone_no。4：呼叫拦截。',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_aliyun_play_record`
--

LOCK TABLES `ims_massage_aliyun_play_record` WRITE;
/*!40000 ALTER TABLE `ims_massage_aliyun_play_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_aliyun_play_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_aliyun_reminder_record`
--

DROP TABLE IF EXISTS `ims_massage_aliyun_reminder_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_aliyun_reminder_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `create_time` bigint(11) DEFAULT '0',
  `res` text COMMENT '通知结果',
  `status` tinyint(3) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_aliyun_reminder_record`
--

LOCK TABLES `ims_massage_aliyun_reminder_record` WRITE;
/*!40000 ALTER TABLE `ims_massage_aliyun_reminder_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_aliyun_reminder_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_article_connect`
--

DROP TABLE IF EXISTS `ims_massage_article_connect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_article_connect` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT '0',
  `field_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_article_connect`
--

LOCK TABLES `ims_massage_article_connect` WRITE;
/*!40000 ALTER TABLE `ims_massage_article_connect` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_article_connect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_article_form_field`
--

DROP TABLE IF EXISTS `ims_massage_article_form_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_article_form_field` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `field_type` int(11) DEFAULT '1',
  `title` varchar(128) CHARACTER SET utf8mb4 DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `top` int(11) DEFAULT '0',
  `is_required` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_article_form_field`
--

LOCK TABLES `ims_massage_article_form_field` WRITE;
/*!40000 ALTER TABLE `ims_massage_article_form_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_article_form_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_article_list`
--

DROP TABLE IF EXISTS `ims_massage_article_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_article_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  `text` longtext CHARACTER SET utf8mb4,
  `is_form` tinyint(3) DEFAULT '0',
  `top` int(11) DEFAULT NULL,
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_article_list`
--

LOCK TABLES `ims_massage_article_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_article_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_article_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_article_sub_data`
--

DROP TABLE IF EXISTS `ims_massage_article_sub_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_article_sub_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `sub_id` int(11) DEFAULT '0',
  `field_id` int(11) DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 DEFAULT '',
  `value` varchar(1024) CHARACTER SET utf8mb4 DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `field_type` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_article_sub_data`
--

LOCK TABLES `ims_massage_article_sub_data` WRITE;
/*!40000 ALTER TABLE `ims_massage_article_sub_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_article_sub_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_article_sub_list`
--

DROP TABLE IF EXISTS `ims_massage_article_sub_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_article_sub_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `create_time` bigint(11) DEFAULT '0',
  `article_id` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_article_sub_list`
--

LOCK TABLES `ims_massage_article_sub_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_article_sub_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_article_sub_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_broker_list`
--

DROP TABLE IF EXISTS `ims_massage_broker_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_broker_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `name` varchar(32) DEFAULT '',
  `mobile` varchar(32) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `update_time` bigint(11) DEFAULT '0',
  `text` varchar(625) DEFAULT '',
  `sh_text` varchar(625) DEFAULT '',
  `sh_time` bigint(11) DEFAULT '0',
  `balance` decimal(10,2) DEFAULT '0.00',
  `total_cash` decimal(10,2) DEFAULT '0.00' COMMENT '总共佣金',
  `cash` decimal(10,2) DEFAULT '0.00' COMMENT '可用佣金',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='经纪人列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_broker_list`
--

LOCK TABLES `ims_massage_broker_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_broker_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_broker_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_channel_cash_water`
--

DROP TABLE IF EXISTS `ims_massage_channel_cash_water`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_channel_cash_water` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道商id',
  `before` decimal(10,2) DEFAULT '0.00' COMMENT '前',
  `change` decimal(10,2) DEFAULT '0.00' COMMENT '改变值',
  `after` decimal(10,2) DEFAULT '0.00' COMMENT '后',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='渠道商手动余额变动记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_channel_cash_water`
--

LOCK TABLES `ims_massage_channel_cash_water` WRITE;
/*!40000 ALTER TABLE `ims_massage_channel_cash_water` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_channel_cash_water` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_channel_cate`
--

DROP TABLE IF EXISTS `ims_massage_channel_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_channel_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='渠道商分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_channel_cate`
--

LOCK TABLES `ims_massage_channel_cate` WRITE;
/*!40000 ALTER TABLE `ims_massage_channel_cate` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_channel_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_channel_list`
--

DROP TABLE IF EXISTS `ims_massage_channel_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_channel_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cate_id` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4,
  `sh_text` text CHARACTER SET utf8mb4,
  `sh_time` bigint(11) DEFAULT '0',
  `user_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT '',
  `mobile` varchar(32) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `total_cash` decimal(10,2) DEFAULT '0.00',
  `cash` decimal(10,2) DEFAULT '0.00',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '提成百分比',
  `channel_bind_time` int(11) DEFAULT '0' COMMENT '时效性/小时',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='渠道商';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_channel_list`
--

LOCK TABLES `ims_massage_channel_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_channel_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_channel_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_channel_staff_list`
--

DROP TABLE IF EXISTS `ims_massage_channel_staff_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_channel_staff_list` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '提成百分比',
  `qr_path` varchar(255) NOT NULL DEFAULT '' COMMENT '员工二维码地址',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道商id',
  `channel_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道商用户id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '绑定用户id',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='渠道商员工绑定表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_channel_staff_list`
--

LOCK TABLES `ims_massage_channel_staff_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_channel_staff_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_channel_staff_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_coach_entry_agreement`
--

DROP TABLE IF EXISTS `ims_massage_coach_entry_agreement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_coach_entry_agreement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `registration_agreement` text COMMENT '注册协议',
  `billing_rules` text COMMENT '计费',
  `legal_notice` text COMMENT '法律申明',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='向导入驻协议';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_coach_entry_agreement`
--

LOCK TABLES `ims_massage_coach_entry_agreement` WRITE;
/*!40000 ALTER TABLE `ims_massage_coach_entry_agreement` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_coach_entry_agreement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_coach_time_log`
--

DROP TABLE IF EXISTS `ims_massage_coach_time_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_coach_time_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `coach_id` int(11) DEFAULT NULL,
  `start_time` varchar(32) DEFAULT '',
  `end_time` varchar(32) DEFAULT '',
  `time` int(11) DEFAULT '0',
  `date` varchar(32) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `is_work` int(11) DEFAULT '0',
  `is_admin` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=766 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_coach_time_log`
--

LOCK TABLES `ims_massage_coach_time_log` WRITE;
/*!40000 ALTER TABLE `ims_massage_coach_time_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_coach_time_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_coach_work_log`
--

DROP TABLE IF EXISTS `ims_massage_coach_work_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_coach_work_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `coach_id` int(11) DEFAULT NULL,
  `date` varchar(11) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `time` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `true_time` bigint(11) DEFAULT '0',
  `start_time` varchar(32) DEFAULT '',
  `end_time` varchar(32) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `coach_id` (`coach_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=689536 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_coach_work_log`
--

LOCK TABLES `ims_massage_coach_work_log` WRITE;
/*!40000 ALTER TABLE `ims_massage_coach_work_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_coach_work_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_config`
--

DROP TABLE IF EXISTS `ims_massage_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `dynamic_check` tinyint(3) DEFAULT '1' COMMENT '1手动审核 2自动',
  `dynamic_comment_check` tinyint(3) DEFAULT '1',
  `app_download_img` varchar(255) DEFAULT '' COMMENT 'app下载图片',
  `android_link` varchar(255) DEFAULT '' COMMENT '安卓下载链接',
  `ios_link` varchar(255) DEFAULT '' COMMENT 'ios下载链接',
  `dynamic_status` tinyint(3) DEFAULT '1' COMMENT '动态开关',
  `clock_cash_status` tinyint(3) DEFAULT '0' COMMENT '加钟返佣金',
  `balance_cash` tinyint(3) DEFAULT '0' COMMENT '余额返回佣金',
  `balance_integral` tinyint(3) DEFAULT '1' COMMENT '余额返回积分',
  `balance_balance` int(11) DEFAULT '100' COMMENT '返回的比例',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_config`
--

LOCK TABLES `ims_massage_config` WRITE;
/*!40000 ALTER TABLE `ims_massage_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_config_setting`
--

DROP TABLE IF EXISTS `ims_massage_config_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_config_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `key` varchar(64) DEFAULT '',
  `value` varchar(1024) CHARACTER SET utf8mb4 DEFAULT '',
  `text` varchar(255) DEFAULT '' COMMENT '备注',
  `default_value` varchar(255) CHARACTER SET utf8mb4 DEFAULT '',
  `field_type` int(32) DEFAULT '1' COMMENT '1数字 2 字符串',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_config_setting`
--

LOCK TABLES `ims_massage_config_setting` WRITE;
/*!40000 ALTER TABLE `ims_massage_config_setting` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_config_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_distribution_config`
--

DROP TABLE IF EXISTS `ims_massage_distribution_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_distribution_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT '',
  `balance` int(11) DEFAULT '0',
  `top` int(11) DEFAULT '0',
  `balance_name` varchar(255) DEFAULT '',
  `obj_name` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_distribution_config`
--

LOCK TABLES `ims_massage_distribution_config` WRITE;
/*!40000 ALTER TABLE `ims_massage_distribution_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_distribution_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_distribution_list`
--

DROP TABLE IF EXISTS `ims_massage_distribution_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_distribution_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `user_name` varchar(32) CHARACTER SET utf8mb4 DEFAULT '',
  `mobile` varchar(32) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `text` varchar(625) CHARACTER SET utf8mb4 DEFAULT '',
  `sh_text` varchar(625) DEFAULT '',
  `sh_time` bigint(11) DEFAULT '0',
  `pid` int(11) DEFAULT '0' COMMENT '分享上级id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_distribution_list`
--

LOCK TABLES `ims_massage_distribution_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_distribution_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_distribution_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_dynamic_comment`
--

DROP TABLE IF EXISTS `ims_massage_dynamic_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_dynamic_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `dynamic_id` int(11) DEFAULT '0',
  `text` text CHARACTER SET utf8mb4,
  `comment_id` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `have_look` tinyint(3) DEFAULT '0',
  `coach_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_dynamic_comment`
--

LOCK TABLES `ims_massage_dynamic_comment` WRITE;
/*!40000 ALTER TABLE `ims_massage_dynamic_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_dynamic_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_dynamic_follow`
--

DROP TABLE IF EXISTS `ims_massage_dynamic_follow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_dynamic_follow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `coach_id` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `have_look` tinyint(3) DEFAULT '0',
  `dynamic_id` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `dynamic_num` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_dynamic_follow`
--

LOCK TABLES `ims_massage_dynamic_follow` WRITE;
/*!40000 ALTER TABLE `ims_massage_dynamic_follow` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_dynamic_follow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_dynamic_list`
--

DROP TABLE IF EXISTS `ims_massage_dynamic_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_dynamic_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 DEFAULT '',
  `cover` varchar(255) DEFAULT NULL,
  `imgs` text,
  `text` text CHARACTER SET utf8mb4,
  `lng` varchar(32) DEFAULT '',
  `lat` varchar(32) DEFAULT '',
  `address` varchar(128) DEFAULT '',
  `user_id` int(11) DEFAULT '0',
  `coach_id` int(11) DEFAULT '0' COMMENT 'j技师id',
  `status` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `top` int(11) DEFAULT '0',
  `pv` int(11) DEFAULT '0' COMMENT '浏览数',
  `check_time` bigint(11) DEFAULT '0',
  `check_text` varchar(625) DEFAULT '' COMMENT '拒绝理由',
  `type` tinyint(3) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_dynamic_list`
--

LOCK TABLES `ims_massage_dynamic_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_dynamic_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_dynamic_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_dynamic_thumbs`
--

DROP TABLE IF EXISTS `ims_massage_dynamic_thumbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_dynamic_thumbs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `dynamic_id` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `have_look` tinyint(3) DEFAULT '0',
  `coach_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_dynamic_thumbs`
--

LOCK TABLES `ims_massage_dynamic_thumbs` WRITE;
/*!40000 ALTER TABLE `ims_massage_dynamic_thumbs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_dynamic_thumbs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_dynamic_watch_record`
--

DROP TABLE IF EXISTS `ims_massage_dynamic_watch_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_dynamic_watch_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `dynamic_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_dynamic_watch_record`
--

LOCK TABLES `ims_massage_dynamic_watch_record` WRITE;
/*!40000 ALTER TABLE `ims_massage_dynamic_watch_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_dynamic_watch_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_help_notice_config`
--

DROP TABLE IF EXISTS `ims_massage_help_notice_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_help_notice_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `help_phone` text COMMENT '求救电话',
  `help_user_id` text COMMENT '求救人',
  `help_voice` varchar(255) DEFAULT '' COMMENT '求救录音',
  `reminder_admin_status` tinyint(3) DEFAULT '1' COMMENT '来电通知是否通知管理员',
  `reminder_notice_admin` tinyint(3) DEFAULT '0' COMMENT '来电通知是否通知平台',
  `reminder_notice_phone` text CHARACTER SET utf8mb4 COMMENT '来电通知电话',
  `reminder_agent_status` tinyint(3) DEFAULT '1' COMMENT '来电通知是否通知代理商',
  `short_admin_status` tinyint(3) DEFAULT '1' COMMENT '短信通知是否通知管理员',
  `short_notice_admin` tinyint(3) DEFAULT '0' COMMENT '短信通知是否通知平台',
  `tmpl_admin_status` tinyint(3) DEFAULT '1' COMMENT '模版消息是否通知管理员',
  `tmpl_agent_status` tinyint(3) DEFAULT '1' COMMENT '模版消息是否通知代理商',
  `tmpl_notice_admin` tinyint(3) DEFAULT '0' COMMENT '模版消息是否通知平台',
  `short_agent_status` tinyint(3) DEFAULT '1' COMMENT '短信通知是否通知代理商',
  `order_tmpl_agent_status` tinyint(3) DEFAULT '0' COMMENT '下单模版消息是否通知代理商',
  `order_tmpl_admin_status` tinyint(3) DEFAULT '0' COMMENT '下单模版消息是否通知管理员',
  `order_tmpl_notice_admin` tinyint(3) DEFAULT '0' COMMENT '下单模版消息是否通知平台',
  `order_tmpl_text` text COMMENT '下单模版消息通知人员',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_help_notice_config`
--

LOCK TABLES `ims_massage_help_notice_config` WRITE;
/*!40000 ALTER TABLE `ims_massage_help_notice_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_help_notice_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_integral_list`
--

DROP TABLE IF EXISTS `ims_massage_integral_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_integral_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `coach_id` int(11) DEFAULT NULL,
  `integral` double(10,2) DEFAULT '0.00',
  `type` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `order_id` int(11) DEFAULT '0',
  `balance` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `user_cash` decimal(10,2) DEFAULT '0.00' COMMENT '用户充值金额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_integral_list`
--

LOCK TABLES `ims_massage_integral_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_integral_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_integral_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_join_form`
--

DROP TABLE IF EXISTS `ims_massage_join_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_join_form` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `name` varchar(16) DEFAULT '' COMMENT '手机号',
  `mobile` varchar(16) DEFAULT '' COMMENT '手机号',
  `city` varchar(32) DEFAULT '' COMMENT '城市',
  `status` tinyint(3) DEFAULT '1' COMMENT '1未读 2已读',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='加盟表单提交记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_join_form`
--

LOCK TABLES `ims_massage_join_form` WRITE;
/*!40000 ALTER TABLE `ims_massage_join_form` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_join_form` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_member_card_list`
--

DROP TABLE IF EXISTS `ims_massage_member_card_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_member_card_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` char(32) DEFAULT '' COMMENT '套餐名字',
  `day` int(11) DEFAULT '0' COMMENT '多少天内有效',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '套餐价格',
  `init_price` decimal(10,2) DEFAULT '0.00' COMMENT '划线价格',
  `icon` char(32) DEFAULT '' COMMENT '套餐标签',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态 1开启 0关闭',
  `top` int(11) DEFAULT '0' COMMENT '排序值',
  `text` text COMMENT '权益',
  `create_time` bigint(12) DEFAULT '0' COMMENT '创建时间',
  `update_time` bigint(11) DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员卡';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_member_card_list`
--

LOCK TABLES `ims_massage_member_card_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_member_card_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_member_card_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_member_config`
--

DROP TABLE IF EXISTS `ims_massage_member_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_member_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `status` tinyint(3) DEFAULT '0' COMMENT '状态0关闭 1开启',
  `discount` decimal(10,1) DEFAULT '9.9' COMMENT '会员卡折扣',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '会员卡返佣',
  `text` longtext COMMENT '会员卡协议',
  `integra` int(11) DEFAULT '0' COMMENT '积分抵扣1元对应多少积分',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员卡配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_member_config`
--

LOCK TABLES `ims_massage_member_config` WRITE;
/*!40000 ALTER TABLE `ims_massage_member_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_member_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_member_order_integral`
--

DROP TABLE IF EXISTS `ims_massage_member_order_integral`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_member_order_integral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '所得积分',
  `order_type` tinyint(3) DEFAULT '1' COMMENT '变动类型 1预约订单 2邀约订单',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户积分流水';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_member_order_integral`
--

LOCK TABLES `ims_massage_member_order_integral` WRITE;
/*!40000 ALTER TABLE `ims_massage_member_order_integral` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_member_order_integral` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_member_order_list`
--

DROP TABLE IF EXISTS `ims_massage_member_order_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_member_order_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `card_id` int(11) DEFAULT '0' COMMENT '会员卡id',
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `status` tinyint(3) DEFAULT '1' COMMENT '订单状态 1待支付 2已支付',
  `order_code` varchar(255) DEFAULT '' COMMENT '订单编号',
  `title` char(32) DEFAULT '' COMMENT '套餐名字',
  `day` int(11) DEFAULT '0' COMMENT '多少天内有效',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '套餐价格',
  `pay_model` int(11) DEFAULT '1' COMMENT '支付方式 1微信支付 2支付宝支付',
  `pay_time` bigint(11) DEFAULT '0' COMMENT '支付时间',
  `pay_price` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '商户订单号',
  `start_time` bigint(11) DEFAULT '0' COMMENT '开始时间',
  `end_time` bigint(11) DEFAULT '0' COMMENT '结束时间',
  `share_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '推广人id',
  `share_balance` decimal(10,2) DEFAULT '0.00' COMMENT '推广人比例',
  `share_cash` decimal(10,2) DEFAULT '0.00' COMMENT '推广人佣金',
  `create_time` bigint(12) DEFAULT '0' COMMENT '创建时间',
  `update_time` bigint(11) DEFAULT '0' COMMENT '编辑时间',
  `app_pay` int(10) DEFAULT '0' COMMENT '是否app',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员卡订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_member_order_list`
--

LOCK TABLES `ims_massage_member_order_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_member_order_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_member_order_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_member_user_integral`
--

DROP TABLE IF EXISTS `ims_massage_member_user_integral`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_member_user_integral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `before` int(11) NOT NULL DEFAULT '0' COMMENT '之前积分',
  `change` int(11) NOT NULL DEFAULT '0' COMMENT '变动积分',
  `after` int(11) NOT NULL DEFAULT '0' COMMENT '之后积分',
  `type` tinyint(3) DEFAULT '1' COMMENT '变动类型 1预约订单 2邀约订单 3套餐订单',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `add` tinyint(4) DEFAULT '0' COMMENT '是否增加积分',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户积分流水';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_member_user_integral`
--

LOCK TABLES `ims_massage_member_user_integral` WRITE;
/*!40000 ALTER TABLE `ims_massage_member_user_integral` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_member_user_integral` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_order_coach_change_logs`
--

DROP TABLE IF EXISTS `ims_massage_order_coach_change_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_order_coach_change_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `old_coach_id` int(11) DEFAULT '0',
  `now_coach_id` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `now_coach_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT '',
  `car_price` decimal(10,2) DEFAULT NULL,
  `init_coach_id` int(11) DEFAULT '0' COMMENT '第一个技师的id',
  `have_car_price` tinyint(3) DEFAULT '0' COMMENT '是否已经给了车费',
  `text` varchar(625) CHARACTER SET utf8mb4 DEFAULT '',
  `old_coach_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT '',
  `start_time` bigint(11) DEFAULT '0' COMMENT '原来订单的开始时间',
  `end_time` bigint(11) DEFAULT '0' COMMENT '原来订单的结束时间',
  `status` tinyint(3) DEFAULT '1',
  `pay_type` int(11) DEFAULT '2',
  `old_coach_mobile` varchar(32) DEFAULT '',
  `now_coach_mobile` varchar(32) DEFAULT '',
  `is_new` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='订单技师移交记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_order_coach_change_logs`
--

LOCK TABLES `ims_massage_order_coach_change_logs` WRITE;
/*!40000 ALTER TABLE `ims_massage_order_coach_change_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_order_coach_change_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_order_control_log`
--

DROP TABLE IF EXISTS `ims_massage_order_control_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_order_control_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `admin_control` tinyint(3) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `pay_type` tinyint(3) DEFAULT '0',
  `old_pay_type` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4433 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='订单操作日志';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_order_control_log`
--

LOCK TABLES `ims_massage_order_control_log` WRITE;
/*!40000 ALTER TABLE `ims_massage_order_control_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_order_control_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_role_admin`
--

DROP TABLE IF EXISTS `ims_massage_role_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_role_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT '0',
  `role_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_role_admin`
--

LOCK TABLES `ims_massage_role_admin` WRITE;
/*!40000 ALTER TABLE `ims_massage_role_admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_role_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_role_list`
--

DROP TABLE IF EXISTS `ims_massage_role_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_role_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(64) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_role_list`
--

LOCK TABLES `ims_massage_role_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_role_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_role_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_role_node`
--

DROP TABLE IF EXISTS `ims_massage_role_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_role_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT '0' COMMENT '角色id',
  `node` varchar(625) DEFAULT '',
  `auth` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3274 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_role_node`
--

LOCK TABLES `ims_massage_role_node` WRITE;
/*!40000 ALTER TABLE `ims_massage_role_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_role_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_send_msg_config`
--

DROP TABLE IF EXISTS `ims_massage_send_msg_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_send_msg_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `help_tmpl_id` varchar(225) DEFAULT '',
  `gzh_appid` varchar(64) DEFAULT '',
  `order_tmp_id` varchar(225) DEFAULT '',
  `cancel_tmp_id` varchar(225) DEFAULT '',
  `coachupdate_tmp_id` varchar(225) DEFAULT '',
  `apply_tmp_id` varchar(255) DEFAULT '' COMMENT '报名通知',
  `apply_ok_tmp_id` varchar(255) DEFAULT '' COMMENT '报名通过通知',
  `apply_no_tmp_id` varchar(255) DEFAULT '' COMMENT '报名拒绝通知',
  `order_service_tmpl_id` varchar(225) DEFAULT '' COMMENT '订单服务相关的通知',
  `refund_pass_tmpl_id` varchar(225) DEFAULT '' COMMENT '通知用户退款通过模板id',
  `refund_nopass_tmpl_id` varchar(225) DEFAULT '' COMMENT '通知用户退款不通过模板id',
  `demand_coach_rob_tmpl_id` varchar(225) DEFAULT '' COMMENT '需求技师抢单模板id',
  `refund_apply_coach_tmpl_id` varchar(225) DEFAULT '' COMMENT '用户申请退款通知技师模板id',
  `pay_success_tmpl_id` varchar(225) DEFAULT '' COMMENT '支付成功通知',
  `integral_tmpl_id` varchar(225) DEFAULT '' COMMENT '积分收益通知',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_send_msg_config`
--

LOCK TABLES `ims_massage_send_msg_config` WRITE;
/*!40000 ALTER TABLE `ims_massage_send_msg_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_send_msg_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_address`
--

DROP TABLE IF EXISTS `ims_massage_service_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_name` varchar(64) DEFAULT '',
  `mobile` varchar(32) DEFAULT '',
  `province` varchar(64) DEFAULT '',
  `city` varchar(64) DEFAULT '',
  `area` varchar(64) DEFAULT '',
  `status` tinyint(3) DEFAULT '0',
  `lng` varchar(64) DEFAULT '0',
  `lat` varchar(64) DEFAULT '0',
  `address` varchar(255) DEFAULT '',
  `top` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `address_info` varchar(625) DEFAULT '',
  `user_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_address`
--

LOCK TABLES `ims_massage_service_address` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_balance_card`
--

DROP TABLE IF EXISTS `ims_massage_service_balance_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_balance_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `price` double(10,2) DEFAULT '0.00' COMMENT '售卖价格',
  `true_price` double(10,2) DEFAULT '0.00' COMMENT '实际价格',
  `top` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_balance_card`
--

LOCK TABLES `ims_massage_service_balance_card` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_balance_card` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_balance_card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_balance_order_list`
--

DROP TABLE IF EXISTS `ims_massage_service_balance_order_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_balance_order_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `order_code` varchar(255) DEFAULT '',
  `transaction_id` varchar(255) DEFAULT '',
  `pay_price` double(10,2) DEFAULT '0.00',
  `sale_price` double(10,2) DEFAULT '0.00',
  `true_price` double(10,2) DEFAULT '0.00',
  `create_time` bigint(12) DEFAULT '0',
  `pay_time` bigint(12) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `title` varchar(255) DEFAULT '',
  `card_id` int(11) DEFAULT '0',
  `now_balance` double(10,2) DEFAULT '0.00' COMMENT '当前余额',
  `app_pay` tinyint(3) DEFAULT '0',
  `coach_id` int(11) DEFAULT '0',
  `integral` double(10,2) DEFAULT '0.00' COMMENT '积分',
  `pay_model` int(11) DEFAULT '1',
  `rebates_balance` int(11) DEFAULT '0' COMMENT '返佣比例',
  `admin_user` int(11) DEFAULT '0' COMMENT '后台操作人',
  `type` tinyint(3) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_balance_order_list`
--

LOCK TABLES `ims_massage_service_balance_order_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_balance_order_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_balance_order_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_balance_refund_order`
--

DROP TABLE IF EXISTS `ims_massage_service_balance_refund_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_balance_refund_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `order_code` varchar(255) DEFAULT '',
  `transaction_id` varchar(255) DEFAULT '',
  `card_id` int(11) DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `apply_price` double(10,2) DEFAULT '0.00',
  `refund_price` double(10,2) DEFAULT '0.00',
  `title` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `sh_time` bigint(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_balance_refund_order`
--

LOCK TABLES `ims_massage_service_balance_refund_order` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_balance_refund_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_balance_refund_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_balance_water`
--

DROP TABLE IF EXISTS `ims_massage_service_balance_water`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_balance_water` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '1' COMMENT '1充值,2消费',
  `add` tinyint(3) DEFAULT '0',
  `price` double(10,2) DEFAULT '0.00' COMMENT '多少钱',
  `create_time` bigint(12) DEFAULT '0',
  `before_balance` double(10,2) DEFAULT '0.00',
  `after_balance` double(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3290 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_balance_water`
--

LOCK TABLES `ims_massage_service_balance_water` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_balance_water` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_balance_water` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_banner`
--

DROP TABLE IF EXISTS `ims_massage_service_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `img` varchar(128) DEFAULT '',
  `top` int(11) DEFAULT '1',
  `link` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `connect_type` int(11) DEFAULT '1' COMMENT '1查看大图；2文章（默认1）type_id  关联内容id',
  `type_id` int(11) DEFAULT '1',
  `banner_type` tinyint(3) DEFAULT '1' COMMENT 'banner类型 1图片 2视频',
  `video_url` varchar(225) DEFAULT '' COMMENT '视频地址',
  `service_type` int(11) DEFAULT '0' COMMENT '服务分类id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_banner`
--

LOCK TABLES `ims_massage_service_banner` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_banner` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_car`
--

DROP TABLE IF EXISTS `ims_massage_service_car`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_car` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `coach_id` int(11) DEFAULT '0',
  `service_id` int(11) DEFAULT '0',
  `num` int(11) DEFAULT '1',
  `status` tinyint(3) DEFAULT '1',
  `order_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2187 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_car`
--

LOCK TABLES `ims_massage_service_car` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_car` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_car` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_car_price`
--

DROP TABLE IF EXISTS `ims_massage_service_car_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_car_price` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `distance_free` double(11,2) DEFAULT '0.00' COMMENT '多少公里免费',
  `start_price` double(10,2) DEFAULT '9.00' COMMENT '起步价',
  `start_distance` double(10,2) DEFAULT '9.00' COMMENT '起步距离',
  `distance_price` double(10,2) DEFAULT '1.90' COMMENT '每公里多少钱',
  `invented_distance` double(10,2) DEFAULT '0.00' COMMENT '虚拟里程',
  `city_id` int(11) DEFAULT '0' COMMENT '城市id',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_car_price`
--

LOCK TABLES `ims_massage_service_car_price` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_car_price` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_car_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_cash_list`
--

DROP TABLE IF EXISTS `ims_massage_service_cash_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_cash_list` (
  `id` int(11) NOT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT '1' COMMENT '1加盟商 2技师 3分销商',
  `user_id` int(11) DEFAULT '0',
  `cash` decimal(10,2) DEFAULT '0.00',
  `order_id` int(11) DEFAULT '0',
  `under_user` int(11) DEFAULT '0' COMMENT '来源',
  `status` int(11) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `balance` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_cash_list`
--

LOCK TABLES `ims_massage_service_cash_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_cash_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_cash_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_city_list`
--

DROP TABLE IF EXISTS `ims_massage_service_city_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_city_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(64) DEFAULT '',
  `lng` varchar(32) DEFAULT '',
  `lat` varchar(32) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `true_name` varchar(64) DEFAULT '',
  `pid` int(11) DEFAULT '0',
  `city_type` tinyint(3) DEFAULT '1',
  `province` varchar(64) DEFAULT '',
  `city` varchar(64) DEFAULT '',
  `area` varchar(64) DEFAULT '',
  `province_code` varchar(64) DEFAULT '',
  `city_code` varchar(64) DEFAULT '',
  `area_code` varchar(64) DEFAULT '',
  `winner_appid` varchar(64) DEFAULT '' COMMENT '云信应用的appid',
  `winner_token` varchar(128) DEFAULT '' COMMENT '云信应用的token',
  `is_screen` tinyint(11) DEFAULT '0' COMMENT '是否开启筛选',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_city_list`
--

LOCK TABLES `ims_massage_service_city_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_city_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_city_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_appeal`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_appeal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_appeal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coach_id` int(11) DEFAULT '0' COMMENT '技师id',
  `uniacid` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `order_code` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '订单号',
  `order_id` int(11) DEFAULT '0' COMMENT '订单id',
  `content` text COMMENT '内容',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态 1待处理 2已处理',
  `reply_content` text CHARACTER SET utf8 COMMENT '回复内容',
  `reply_date` datetime DEFAULT NULL COMMENT '回复时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='技师申诉';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_appeal`
--

LOCK TABLES `ims_massage_service_coach_appeal` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_appeal` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_appeal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_collect`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_collect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_collect` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `coach_id` int(11) DEFAULT '0',
  `create_time` bigint(12) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_collect`
--

LOCK TABLES `ims_massage_service_coach_collect` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_collect` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_collect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_feedback`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '类型名称',
  `coach_id` int(11) DEFAULT '0' COMMENT '修改为用户id',
  `uniacid` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `order_code` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '订单号',
  `content` text COMMENT '内容',
  `images` text CHARACTER SET utf8 COMMENT '图片地址 json',
  `video_url` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '视频地址',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态 1待处理 2已处理',
  `reply_content` text CHARACTER SET utf8 COMMENT '回复内容',
  `reply_date` datetime DEFAULT NULL COMMENT '回复时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='技师问题反馈';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_feedback`
--

LOCK TABLES `ims_massage_service_coach_feedback` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_level`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  `time_long` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `create_time` bigint(12) DEFAULT '0',
  `balance` int(11) DEFAULT '0' COMMENT '抽成比例',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '最低业绩',
  `add_balance` int(11) DEFAULT '0' COMMENT '加钟率',
  `integral` double(10,2) DEFAULT '0.00' COMMENT '积分',
  `top` int(11) DEFAULT '0' COMMENT '等级',
  `online_time` int(11) DEFAULT '0' COMMENT '在线时长小时',
  `agent_article_id` int(11) DEFAULT '0' COMMENT '代理商入住的文章id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_level`
--

LOCK TABLES `ims_massage_service_coach_level` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_level` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_list`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `coach_name` varchar(255) DEFAULT '' COMMENT '名称',
  `user_id` int(11) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `sex` tinyint(3) DEFAULT '0' COMMENT '性别',
  `work_time` bigint(12) DEFAULT '0' COMMENT '从业年份',
  `city` varchar(255) DEFAULT '' COMMENT '城市',
  `lng` varchar(255) DEFAULT '',
  `lat` varchar(255) DEFAULT '',
  `address` varchar(625) DEFAULT '' COMMENT '详细地址',
  `text` varchar(625) DEFAULT '' COMMENT '简介',
  `id_card` varchar(625) DEFAULT '',
  `license` text COMMENT '执照',
  `work_img` varchar(255) DEFAULT '' COMMENT '工作照',
  `self_img` text COMMENT '个人照片',
  `is_work` tinyint(3) DEFAULT '0' COMMENT '是否工作',
  `start_time` varchar(32) DEFAULT '',
  `end_time` varchar(32) DEFAULT '',
  `service_price` double(10,2) DEFAULT '0.00',
  `car_price` double(10,2) DEFAULT '0.00',
  `id_code` varchar(255) DEFAULT '',
  `sh_text` varchar(1024) DEFAULT '',
  `sh_time` bigint(11) DEFAULT '0',
  `star` decimal(10,1) DEFAULT '0.0',
  `admin_add` tinyint(3) DEFAULT '0' COMMENT '是否是后台添加',
  `admin_id` int(11) DEFAULT '0' COMMENT '加盟商id',
  `city_id` int(11) DEFAULT '0',
  `video` varchar(255) DEFAULT '' COMMENT '视频',
  `is_update` tinyint(3) DEFAULT '0',
  `integral` decimal(10,2) DEFAULT '0.00' COMMENT '积分',
  `order_num` int(11) DEFAULT '0' COMMENT '技师虚拟订单数量',
  `recommend` tinyint(3) DEFAULT '0' COMMENT '是否是推荐技师',
  `balance_cash` decimal(10,2) DEFAULT '0.00' COMMENT '邀请充值余额返回佣金',
  `index_top` tinyint(3) DEFAULT '0' COMMENT '虚拟排序',
  `coach_position` tinyint(3) DEFAULT '0' COMMENT '实时定位',
  `near_time` bigint(11) DEFAULT '0',
  `agent_type` tinyint(3) DEFAULT '1' COMMENT '1代理商 2合伙人',
  `partner_id` int(11) DEFAULT '0' COMMENT '合伙人id',
  `partner_time` bigint(11) DEFAULT '0' COMMENT '合伙人绑定时间',
  `store_id` int(11) DEFAULT '0' COMMENT '挂靠门店',
  `birthday` bigint(11) DEFAULT '0' COMMENT '生日',
  `constellation` varchar(32) DEFAULT '' COMMENT '星座',
  `height` int(10) DEFAULT '0' COMMENT '身高',
  `weight` int(10) DEFAULT '0' COMMENT '体重',
  `tag_id` int(11) DEFAULT '0',
  `coach_index` tinyint(3) DEFAULT '0' COMMENT '排序',
  `cash_balance` tinyint(3) DEFAULT '0' COMMENT '固定分销比例',
  `nickname` varchar(32) DEFAULT '' COMMENT '昵称',
  `min_price` decimal(10,2) DEFAULT '0.00' COMMENT '最低价',
  `model_img` varchar(1024) DEFAULT '' COMMENT '模特照',
  `broker_id` int(10) DEFAULT '0' COMMENT '经纪人id',
  `attitude_star` decimal(10,1) DEFAULT '0.0' COMMENT '态度评分',
  `speed_star` decimal(10,1) DEFAULT '0.0' COMMENT '速度评分',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=272 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_list`
--

LOCK TABLES `ims_massage_service_coach_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_police`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_police`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_police` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `coach_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `text` varchar(1024) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `have_look` tinyint(3) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `lng` varchar(32) DEFAULT '',
  `lat` varchar(32) DEFAULT '',
  `address` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_police`
--

LOCK TABLES `ims_massage_service_coach_police` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_police` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_police` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_tag`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `status` tinyint(3) DEFAULT '1',
  `create_time` int(11) DEFAULT '0',
  `uniacid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='技师个性标签表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_tag`
--

LOCK TABLES `ims_massage_service_coach_tag` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_time`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coach_id` int(11) DEFAULT '0' COMMENT '技师id',
  `date` char(16) DEFAULT '' COMMENT '日期  格式Y-m-d',
  `info` text COMMENT '日期节点详情',
  `hours` int(11) DEFAULT '0' COMMENT '日期节点在线时长',
  `uniacid` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `start_time` varchar(32) DEFAULT '' COMMENT '开始时间',
  `end_time` varchar(32) DEFAULT '' COMMENT '结束时间',
  `max_day` varchar(255) DEFAULT '',
  `time_unit` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `coach_id` (`coach_id`) USING BTREE,
  KEY `date` (`date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='技师时间管理';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_time`
--

LOCK TABLES `ims_massage_service_coach_time` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_time_list`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_time_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_time_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_id` int(11) DEFAULT '0' COMMENT '时间管理设置id',
  `time_str` varchar(16) DEFAULT '' COMMENT '开始时间戳',
  `time_str_end` varchar(16) DEFAULT '' COMMENT '结束时间戳',
  `time_text` varchar(16) DEFAULT '' COMMENT '时间',
  `time_texts` varchar(16) DEFAULT '' COMMENT '日期',
  `status` int(3) DEFAULT NULL COMMENT '状态',
  `create_time` bigint(11) DEFAULT '0',
  `coach_id` int(11) DEFAULT '0' COMMENT '技师id',
  `uniacid` int(11) DEFAULT '0',
  `is_click` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `time_id` (`time_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2296 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='技师时间管理时间节点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_time_list`
--

LOCK TABLES `ims_massage_service_coach_time_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_time_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_time_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coach_update`
--

DROP TABLE IF EXISTS `ims_massage_service_coach_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coach_update` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `coach_name` varchar(255) DEFAULT '' COMMENT '名称',
  `user_id` int(11) DEFAULT NULL,
  `coach_id` int(11) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `sex` tinyint(3) DEFAULT '0' COMMENT '性别',
  `work_time` bigint(12) DEFAULT '0' COMMENT '从业年份',
  `city` varchar(255) DEFAULT '' COMMENT '城市',
  `lng` varchar(255) DEFAULT '',
  `lat` varchar(255) DEFAULT '',
  `address` varchar(625) DEFAULT '' COMMENT '详细地址',
  `text` varchar(625) DEFAULT '' COMMENT '简介',
  `id_card` varchar(625) DEFAULT '',
  `license` text COMMENT '执照',
  `work_img` varchar(255) DEFAULT '' COMMENT '工作照',
  `self_img` text COMMENT '个人照片',
  `is_work` tinyint(3) DEFAULT '1' COMMENT '是否工作',
  `start_time` varchar(32) DEFAULT '',
  `end_time` varchar(32) DEFAULT '',
  `id_code` varchar(255) DEFAULT '',
  `sh_text` varchar(1024) DEFAULT '',
  `sh_time` bigint(11) DEFAULT '0',
  `video` varchar(255) DEFAULT '' COMMENT '视频',
  `city_id` int(11) DEFAULT '0',
  `store_id` int(11) DEFAULT '0' COMMENT '门店id',
  `birthday` bigint(11) DEFAULT '0' COMMENT '生日',
  `constellation` varchar(32) DEFAULT '' COMMENT '星座',
  `height` int(10) DEFAULT '0' COMMENT '身高',
  `weight` int(10) DEFAULT '0' COMMENT '体重',
  `nickname` varchar(32) DEFAULT '' COMMENT '昵称',
  `order_num` int(11) DEFAULT '0',
  `tag_id` int(11) DEFAULT '0',
  `model_img` varchar(1024) DEFAULT '' COMMENT '模特照',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=296 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='技师修改表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coach_update`
--

LOCK TABLES `ims_massage_service_coach_update` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coach_update` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coach_update` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_comment_lable`
--

DROP TABLE IF EXISTS `ims_massage_service_comment_lable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_comment_lable` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT '0',
  `lable_id` int(11) DEFAULT NULL,
  `lable_title` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_comment_lable`
--

LOCK TABLES `ims_massage_service_comment_lable` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_comment_lable` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_comment_lable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coupon`
--

DROP TABLE IF EXISTS `ims_massage_service_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '' COMMENT '名称',
  `type` tinyint(3) DEFAULT '0',
  `full` double(10,2) DEFAULT NULL COMMENT '满多少',
  `discount` double(10,2) DEFAULT NULL COMMENT '减多少',
  `rule` text COMMENT '规则',
  `text` text COMMENT '详情',
  `send_type` tinyint(3) DEFAULT '0' COMMENT '派发方式',
  `time_limit` tinyint(3) DEFAULT '0' COMMENT '时间限制',
  `start_time` bigint(12) DEFAULT '0',
  `end_time` bigint(12) DEFAULT '0',
  `day` int(11) DEFAULT '0' COMMENT '有效期',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(12) DEFAULT '0',
  `top` int(11) DEFAULT '0',
  `stock` int(11) DEFAULT '0' COMMENT '库存',
  `have_send` int(11) DEFAULT '0' COMMENT '已发多少张',
  `i` int(11) DEFAULT '0',
  `user_limit` int(11) DEFAULT '1' COMMENT '1不限制 2新用户',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coupon`
--

LOCK TABLES `ims_massage_service_coupon` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coupon_atv`
--

DROP TABLE IF EXISTS `ims_massage_service_coupon_atv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coupon_atv` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `status` tinyint(3) DEFAULT '0',
  `start_time` bigint(12) DEFAULT '0',
  `end_time` bigint(12) DEFAULT '0',
  `inv_user_num` int(11) DEFAULT '0' COMMENT '邀请好友数量',
  `inv_time` int(11) DEFAULT '0' COMMENT '邀请有效期',
  `atv_num` int(11) DEFAULT '0' COMMENT '发起活动次数',
  `inv_user` int(11) DEFAULT '0' COMMENT '邀请人',
  `to_inv_user` int(11) DEFAULT '0' COMMENT '被邀请人',
  `share_img` varchar(625) DEFAULT '',
  `is_atv_status` tinyint(3) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coupon_atv`
--

LOCK TABLES `ims_massage_service_coupon_atv` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coupon_atv_coupon`
--

DROP TABLE IF EXISTS `ims_massage_service_coupon_atv_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coupon_atv_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `atv_id` int(11) DEFAULT '0',
  `coupon_id` int(11) DEFAULT '0',
  `num` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coupon_atv_coupon`
--

LOCK TABLES `ims_massage_service_coupon_atv_coupon` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coupon_atv_record`
--

DROP TABLE IF EXISTS `ims_massage_service_coupon_atv_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coupon_atv_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `atv_id` int(11) DEFAULT '0' COMMENT '目前只有一个活动,没有什么用',
  `atv_start_time` bigint(11) DEFAULT '0',
  `atv_end_time` bigint(11) DEFAULT NULL,
  `inv_user_num` int(11) DEFAULT '0' COMMENT '邀请好友数量',
  `inv_time` int(11) DEFAULT '0' COMMENT '有效期',
  `end_time` bigint(11) DEFAULT '0',
  `start_time` bigint(11) DEFAULT '0',
  `inv_user` tinyint(3) DEFAULT '0',
  `to_inv_user` tinyint(3) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `num` int(11) DEFAULT '1',
  `share_img` varchar(625) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coupon_atv_record`
--

LOCK TABLES `ims_massage_service_coupon_atv_record` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coupon_atv_record_coupon`
--

DROP TABLE IF EXISTS `ims_massage_service_coupon_atv_record_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coupon_atv_record_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `atv_id` int(11) DEFAULT '0',
  `record_id` int(11) DEFAULT '0' COMMENT '发起的活动id',
  `coupon_id` int(11) DEFAULT '0' COMMENT '优惠券id',
  `num` int(11) DEFAULT '1' COMMENT '张数',
  `status` int(11) DEFAULT '1',
  `success_num` int(11) DEFAULT '0' COMMENT '已发多少张',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coupon_atv_record_coupon`
--

LOCK TABLES `ims_massage_service_coupon_atv_record_coupon` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv_record_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv_record_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coupon_atv_record_list`
--

DROP TABLE IF EXISTS `ims_massage_service_coupon_atv_record_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coupon_atv_record_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `record_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0' COMMENT '发起人',
  `to_inv_id` int(11) DEFAULT '0' COMMENT '被邀请人',
  `create_time` bigint(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coupon_atv_record_list`
--

LOCK TABLES `ims_massage_service_coupon_atv_record_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv_record_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coupon_atv_record_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coupon_goods`
--

DROP TABLE IF EXISTS `ims_massage_service_coupon_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coupon_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `coupon_id` int(11) DEFAULT '0',
  `goods_id` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '0' COMMENT '0平台，用户领取',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=467 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coupon_goods`
--

LOCK TABLES `ims_massage_service_coupon_goods` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coupon_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coupon_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_coupon_record`
--

DROP TABLE IF EXISTS `ims_massage_service_coupon_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_coupon_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `coupon_id` int(11) DEFAULT '0',
  `title` varchar(225) DEFAULT '',
  `type` int(11) DEFAULT '0',
  `full` double(10,2) DEFAULT '0.00',
  `discount` double(10,2) DEFAULT '0.00',
  `start_time` bigint(12) DEFAULT '0',
  `end_time` bigint(13) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(12) DEFAULT '0',
  `num` int(11) DEFAULT '1',
  `use_time` bigint(11) DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `pid` int(11) DEFAULT '0',
  `rule` text,
  `text` text,
  `is_show` tinyint(3) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_coupon_record`
--

LOCK TABLES `ims_massage_service_coupon_record` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_coupon_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_coupon_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_demand_order`
--

DROP TABLE IF EXISTS `ims_massage_service_demand_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_demand_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0' COMMENT '发布人id',
  `content` varchar(500) CHARACTER SET utf8mb4 DEFAULT '',
  `img` text COMMENT '图片  ,分割',
  `ser_id` int(11) DEFAULT '0' COMMENT '服务id',
  `is_hide` tinyint(3) DEFAULT '0' COMMENT '是否隐藏 1是0否',
  `price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `start_time` int(11) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束时间',
  `status` tinyint(3) DEFAULT '1' COMMENT '支付状态 -1取消  0拒绝  1待审核  2待接单  3已接单  4已完成',
  `order_code` varchar(255) DEFAULT '' COMMENT '订单号',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '商户订单号',
  `pay_price` double(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `coach_id` int(11) DEFAULT '0' COMMENT '服务技师',
  `pay_time` bigint(12) DEFAULT '0' COMMENT '支付时间',
  `create_time` bigint(12) DEFAULT '0' COMMENT '创建时间',
  `can_tx_time` int(11) DEFAULT '0' COMMENT '提现时间设置',
  `can_tx_date` int(11) DEFAULT '0' COMMENT '可提现时间',
  `receiving_time` bigint(11) DEFAULT '0' COMMENT '接单时间',
  `order_end_time` bigint(11) DEFAULT '0' COMMENT '订单完成时间',
  `coach_cash` decimal(10,2) DEFAULT '0.00' COMMENT '技师佣金',
  `coach_balance` int(11) DEFAULT '0' COMMENT '技师佣金比例',
  `pay_type` tinyint(3) DEFAULT '1' COMMENT '支付方式 1支付宝 2微信 3余额',
  `lng` varchar(32) DEFAULT '0' COMMENT '经度',
  `lat` varchar(32) DEFAULT '0' COMMENT '维度',
  `address` varchar(255) DEFAULT '' COMMENT '地址',
  `is_pay` tinyint(3) DEFAULT '0' COMMENT '是否支付 1是',
  `check_text` varchar(500) CHARACTER SET utf8mb4 DEFAULT '',
  `check_time` int(11) DEFAULT '0' COMMENT '审核时间',
  `cancel_time` int(11) DEFAULT '0' COMMENT '取消时间',
  `complete_time` int(11) DEFAULT '0' COMMENT '完成时间',
  `phone` varchar(255) DEFAULT '' COMMENT '联系电话',
  `is_refund` tinyint(3) DEFAULT '0',
  `app_pay` tinyint(3) DEFAULT '0',
  `cash_type` tinyint(3) DEFAULT '0' COMMENT '分佣模式 1浮动等级 2固定',
  `admin_balance` int(11) DEFAULT '0' COMMENT '加盟商佣金比例',
  `admin_id` int(11) DEFAULT '0' COMMENT '代理商id',
  `user_cash` decimal(10,2) DEFAULT '0.00' COMMENT '用户分销',
  `company_cash` decimal(10,2) DEFAULT '0.00' COMMENT '平台抽层',
  `admin_cash` decimal(10,2) DEFAULT '0.00' COMMENT '加盟商佣金',
  `partner_id` int(11) DEFAULT '0' COMMENT '合伙人id',
  `user_balance` int(11) DEFAULT '0' COMMENT '用户分销比例',
  `user_fx_id` int(11) DEFAULT '0' COMMENT '用户分销id',
  `is_car` tinyint(3) DEFAULT '0' COMMENT '是否报销车费 1是',
  `member_discount` decimal(10,2) DEFAULT '0.00' COMMENT '会员优惠金额',
  `member_balance` decimal(10,1) DEFAULT '0.0' COMMENT '会员折扣',
  `member_status` tinyint(4) DEFAULT '0' COMMENT '是否有会员折扣',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=582 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户发布需求订单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_demand_order`
--

LOCK TABLES `ims_massage_service_demand_order` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_demand_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_demand_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_demand_order_apply`
--

DROP TABLE IF EXISTS `ims_massage_service_demand_order_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_demand_order_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT '0' COMMENT '需求订单id',
  `coach_id` int(11) DEFAULT '0' COMMENT '技师id',
  `status` tinyint(11) DEFAULT '1' COMMENT '状态 1待处理 2已选择 3已拒绝',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `is_look` tinyint(3) DEFAULT '0' COMMENT '是否查看1是 0否',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=346 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='需求订单申请报名';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_demand_order_apply`
--

LOCK TABLES `ims_massage_service_demand_order_apply` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_demand_order_apply` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_demand_order_apply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_demand_type`
--

DROP TABLE IF EXISTS `ims_massage_service_demand_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_demand_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `top` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态 0下架 1上架 -1删除',
  `create_time` int(11) DEFAULT '0',
  `uniacid` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `img` varchar(255) DEFAULT '' COMMENT '图片',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='服务类型/技能表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_demand_type`
--

LOCK TABLES `ims_massage_service_demand_type` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_demand_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_demand_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_lable`
--

DROP TABLE IF EXISTS `ims_massage_service_lable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_lable` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  `top` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_lable`
--

LOCK TABLES `ims_massage_service_lable` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_lable` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_lable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_many_demand_order`
--

DROP TABLE IF EXISTS `ims_massage_service_many_demand_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_many_demand_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0' COMMENT '发布人id',
  `content` varchar(500) DEFAULT '' COMMENT '内容',
  `img` text COMMENT '图片  ,分割',
  `ser_data` varchar(500) DEFAULT '' COMMENT '服务类型  json',
  `is_hide` tinyint(3) DEFAULT '0' COMMENT '是否隐藏 1是0否',
  `price` decimal(10,2) DEFAULT NULL COMMENT '总价格',
  `start_time` int(11) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束时间',
  `status` tinyint(3) DEFAULT '1' COMMENT '支付状态 -1取消  0拒绝  1待审核  2待接单  3已接单  4已完成',
  `order_code` varchar(255) DEFAULT '' COMMENT '订单号',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '商户订单号',
  `pay_price` double(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `pay_time` bigint(12) DEFAULT '0' COMMENT '支付时间',
  `create_time` bigint(12) DEFAULT '0' COMMENT '创建时间',
  `can_tx_time` int(11) DEFAULT '0' COMMENT '提现时间设置',
  `can_tx_date` int(11) DEFAULT '0' COMMENT '可提现时间',
  `pay_type` tinyint(3) DEFAULT '1' COMMENT '支付方式 1支付宝 2微信 3余额',
  `lng` varchar(32) DEFAULT '0' COMMENT '经度',
  `lat` varchar(32) DEFAULT '0' COMMENT '维度',
  `address` varchar(255) DEFAULT '' COMMENT '地址',
  `is_pay` tinyint(3) DEFAULT '0' COMMENT '是否支付 1是',
  `phone` varchar(255) DEFAULT '' COMMENT '联系电话',
  `cash_type` tinyint(3) DEFAULT '0' COMMENT '分佣模式 1浮动等级 2固定',
  `app_pay` tinyint(3) DEFAULT '0',
  `coach_cash` decimal(10,2) DEFAULT '0.00' COMMENT '技师佣金',
  `coach_balance` int(11) DEFAULT '0' COMMENT '技师佣金比例',
  `is_car` tinyint(3) DEFAULT '0' COMMENT '是否报销车费 1是',
  `member_discount` decimal(10,2) DEFAULT '0.00' COMMENT '会员优惠金额',
  `member_balance` decimal(10,1) DEFAULT '0.0' COMMENT '会员折扣',
  `member_status` tinyint(4) DEFAULT '0' COMMENT '是否有会员折扣',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户一次发布多个需求订单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_many_demand_order`
--

LOCK TABLES `ims_massage_service_many_demand_order` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_many_demand_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_many_demand_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_notice_list`
--

DROP TABLE IF EXISTS `ims_massage_service_notice_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_notice_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT '0',
  `have_look` tinyint(3) DEFAULT '0' COMMENT '是否被查看',
  `type` tinyint(3) DEFAULT '1' COMMENT '1是订单，2是退款',
  `create_time` bigint(11) DEFAULT '0',
  `admin_id` int(11) DEFAULT '0',
  `agent_have_look` int(11) DEFAULT '0' COMMENT '代理商是否查看',
  `is_pop` tinyint(3) DEFAULT '1' COMMENT '是否弹出过弹窗',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2277 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_notice_list`
--

LOCK TABLES `ims_massage_service_notice_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_notice_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_notice_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_address`
--

DROP TABLE IF EXISTS `ims_massage_service_order_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT '0',
  `user_name` varchar(64) DEFAULT '',
  `mobile` varchar(32) DEFAULT '',
  `province` varchar(64) DEFAULT '',
  `city` varchar(64) DEFAULT '',
  `area` varchar(64) DEFAULT '',
  `lng` varchar(32) DEFAULT '0',
  `lat` varchar(32) DEFAULT '0',
  `address` varchar(255) DEFAULT '',
  `address_info` varchar(625) DEFAULT '',
  `address_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1403 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_address`
--

LOCK TABLES `ims_massage_service_order_address` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_comment`
--

DROP TABLE IF EXISTS `ims_massage_service_order_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `order_id` int(11) DEFAULT NULL,
  `star` int(255) DEFAULT '0',
  `text` varchar(625) CHARACTER SET utf8mb4 DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `coach_id` int(11) DEFAULT '0',
  `admin_id` int(11) DEFAULT '0',
  `attitude_star` decimal(10,1) DEFAULT '5.0' COMMENT '态度评分',
  `speed_star` decimal(10,1) DEFAULT '5.0' COMMENT '速度评分',
  `is_hide` tinyint(3) DEFAULT '0' COMMENT '是否匿名 1是',
  `is_admin` tinyint(3) DEFAULT '0' COMMENT '是否后台添加 1是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_comment`
--

LOCK TABLES `ims_massage_service_order_comment` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_comment_goods`
--

DROP TABLE IF EXISTS `ims_massage_service_order_comment_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_comment_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT '0',
  `comment_id` int(11) DEFAULT '0',
  `star` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_comment_goods`
--

LOCK TABLES `ims_massage_service_order_comment_goods` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_comment_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_comment_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_commission`
--

DROP TABLE IF EXISTS `ims_massage_service_order_commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_commission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `top_id` int(11) DEFAULT '0' COMMENT '上级id',
  `order_id` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `order_code` varchar(255) DEFAULT '',
  `cash` double(10,2) DEFAULT NULL COMMENT '佣金',
  `create_time` bigint(12) DEFAULT '0',
  `update_time` bigint(12) DEFAULT '0',
  `cash_time` bigint(11) DEFAULT '0',
  `type` int(11) DEFAULT '1' COMMENT '1分销 2加盟商 3技师 4分销商',
  `balance` decimal(10,2) DEFAULT '0.00',
  `admin_id` int(11) DEFAULT '0' COMMENT '加盟商id',
  `city_type` int(11) DEFAULT '1' COMMENT '1市级 2县级',
  `coach_cash` decimal(10,2) DEFAULT '0.00',
  `car_cash` decimal(10,2) DEFAULT '0.00',
  `cash_status` tinyint(3) DEFAULT '1' COMMENT '代理商是否给线下技师打款',
  `order_type` tinyint(3) DEFAULT '1' COMMENT '订单类型 1服务订单 2邀约订单',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3553 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_commission`
--

LOCK TABLES `ims_massage_service_order_commission` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_commission` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_commission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_commission_goods`
--

DROP TABLE IF EXISTS `ims_massage_service_order_commission_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_commission_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_goods_id` int(11) DEFAULT '0',
  `commission_id` int(11) DEFAULT '0',
  `cash` double(10,2) DEFAULT NULL,
  `balance` int(11) DEFAULT '0',
  `num` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=387 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_commission_goods`
--

LOCK TABLES `ims_massage_service_order_commission_goods` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_commission_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_commission_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_commission_share`
--

DROP TABLE IF EXISTS `ims_massage_service_order_commission_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_commission_share` (
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
  PRIMARY KEY (`id`) USING BTREE,
  KEY `comm_id` (`comm_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=317 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='佣金分摊表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_commission_share`
--

LOCK TABLES `ims_massage_service_order_commission_share` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_commission_share` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_commission_share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_goods_list`
--

DROP TABLE IF EXISTS `ims_massage_service_order_goods_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_goods_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `goods_id` int(11) DEFAULT '0' COMMENT '服务id',
  `goods_name` varchar(255) DEFAULT '' COMMENT '服务名称',
  `goods_cover` varchar(255) DEFAULT '' COMMENT '封面图',
  `price` double(10,2) DEFAULT '0.00',
  `num` int(11) DEFAULT '1' COMMENT '数量',
  `coach_id` int(11) DEFAULT '0',
  `time_long` int(11) DEFAULT '0',
  `can_refund_num` int(11) DEFAULT '0',
  `true_price` double(10,5) DEFAULT '0.00000',
  `pay_type` tinyint(3) DEFAULT '1',
  `status` tinyint(3) DEFAULT '1',
  `coupon_discount` decimal(10,2) DEFAULT '0.00',
  `member_discount` decimal(10,2) DEFAULT '0.00' COMMENT '会员优惠金额',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1486 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_goods_list`
--

LOCK TABLES `ims_massage_service_order_goods_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_goods_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_goods_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_list`
--

DROP TABLE IF EXISTS `ims_massage_service_order_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `order_code` varchar(255) DEFAULT '' COMMENT '订单号',
  `pay_type` tinyint(3) DEFAULT '1',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '商户订单号',
  `pay_price` double(10,2) DEFAULT '0.00',
  `car_price` double(10,2) DEFAULT '0.00' COMMENT '出行费用',
  `true_car_price` double(10,2) DEFAULT '0.00',
  `service_price` double(10,2) DEFAULT '0.00' COMMENT '服务费用',
  `true_service_price` double(10,2) DEFAULT '0.00',
  `coach_id` int(11) DEFAULT '0' COMMENT '服务技师',
  `start_time` bigint(12) DEFAULT '0',
  `end_time` bigint(12) DEFAULT '0',
  `time_long` int(11) DEFAULT '0' COMMENT '服务时长',
  `true_time_long` int(11) DEFAULT '0' COMMENT '真实服务时长出来退款的',
  `pay_time` bigint(12) DEFAULT '0',
  `create_time` bigint(12) DEFAULT '0',
  `text` varchar(625) DEFAULT '',
  `can_tx_time` int(11) DEFAULT '0',
  `can_tx_date` int(11) DEFAULT '0' COMMENT '可提现时间',
  `hx_user` int(11) DEFAULT '0' COMMENT '核销人',
  `have_tx` tinyint(3) DEFAULT '0',
  `balance` double(10,2) DEFAULT '0.00',
  `receiving_time` bigint(11) DEFAULT '0' COMMENT '接单时间',
  `serout_time` bigint(11) DEFAULT '0' COMMENT '出发时间',
  `arrive_time` bigint(11) DEFAULT '0' COMMENT '到达时间',
  `start_service_time` bigint(11) DEFAULT '0' COMMENT '开始服务时间',
  `arrive_img` varchar(625) DEFAULT '' COMMENT '到达拍照',
  `order_end_time` bigint(11) DEFAULT '0' COMMENT '订单核销时间',
  `is_comment` tinyint(3) DEFAULT '0',
  `distance` double(10,2) DEFAULT '0.00' COMMENT '距离',
  `car_type` tinyint(3) DEFAULT '0',
  `coach_refund_time` bigint(11) DEFAULT '0' COMMENT '拒绝接单',
  `coach_refund_code` varchar(64) DEFAULT '',
  `coupon_id` int(11) DEFAULT '0',
  `discount` double(10,2) DEFAULT '0.00' COMMENT '优惠金额',
  `init_service_price` double(10,2) DEFAULT '0.00',
  `over_time` bigint(11) DEFAULT '0',
  `is_show` tinyint(3) DEFAULT '1',
  `arr_address` varchar(225) DEFAULT '',
  `arr_lng` varchar(32) DEFAULT '',
  `arr_lat` varchar(32) DEFAULT '',
  `end_img` varchar(255) DEFAULT '',
  `end_lng` varchar(32) DEFAULT '',
  `end_lat` varchar(32) DEFAULT '',
  `end_address` varchar(255) DEFAULT '',
  `app_pay` int(11) DEFAULT '0',
  `admin_id` int(11) DEFAULT '0' COMMENT '加盟商id',
  `admin_balance` int(11) DEFAULT '0' COMMENT '加盟商佣金比例',
  `admin_cash` decimal(10,2) DEFAULT '0.00' COMMENT '加盟商佣金',
  `coach_balance` int(11) DEFAULT '0' COMMENT '技师佣金比例',
  `coach_cash` decimal(10,2) DEFAULT '0.00' COMMENT '技师佣金',
  `company_cash` decimal(10,2) DEFAULT '0.00' COMMENT '平台抽层',
  `user_cash` decimal(10,2) DEFAULT '0.00' COMMENT '用户分销',
  `channel_id` int(11) DEFAULT '0' COMMENT '渠道商',
  `coach_refund_text` varchar(625) CHARACTER SET utf8mb4 DEFAULT '',
  `trip_start_address` varchar(255) DEFAULT '' COMMENT '技师出发地址',
  `trip_end_address` varchar(255) DEFAULT '' COMMENT '技师到达地址',
  `is_add` tinyint(3) DEFAULT '0' COMMENT '是否是加钟订单',
  `add_pid` int(11) DEFAULT '0' COMMENT '加钟订单的父id',
  `label_time` bigint(11) DEFAULT '0',
  `serout_lng` varchar(32) DEFAULT '',
  `serout_lat` varchar(32) DEFAULT '',
  `serout_address` varchar(32) DEFAULT '',
  `pay_model` int(11) DEFAULT '1',
  `partner_id` int(11) DEFAULT '0' COMMENT '合伙人id',
  `store_id` int(11) DEFAULT '0' COMMENT '门店id',
  `cash_type` tinyint(3) DEFAULT '1' COMMENT '分销类型 1浮动 2固定',
  `is_red` tinyint(3) DEFAULT '1' COMMENT '是否展示小气泡 1是 0已看',
  `user_top_id` int(11) DEFAULT '0' COMMENT '用户分销id',
  `user_balance` int(11) DEFAULT '0' COMMENT '用户分销比例',
  `channel_staff_id` int(11) DEFAULT '0' COMMENT '渠道商员工id',
  `channel_cash` decimal(10,2) DEFAULT '0.00' COMMENT '渠道商提成金额',
  `channel_balance` decimal(10,2) DEFAULT '0.00' COMMENT '渠道商提成比例',
  `channel_staff_balance` decimal(10,2) DEFAULT '0.00' COMMENT '渠道商员工提成比例',
  `hx_type` tinyint(3) DEFAULT '1' COMMENT '核销用户类型1技师 2用户',
  `broker_id` int(10) DEFAULT '0' COMMENT '经纪人id',
  `broker_cash` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人佣金',
  `broker_balance` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人比例',
  `broker_coach_cash` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人向导承担佣金',
  `broker_coach_balance` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人向导承担比例',
  `broker_agent_cash` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人代理商承担佣金',
  `broker_agent_balance` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人代理商承担比例',
  `is_car` tinyint(3) DEFAULT '1' COMMENT '是否计算车费  1开启 0关闭',
  `broker_admin_cash` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人平台承担佣金',
  `broker_admin_balance` decimal(10,2) DEFAULT '0.00' COMMENT '经纪人平台承担佣金比例',
  `is_safe` tinyint(3) DEFAULT '1' COMMENT '该订单是否有跳单风险 1没有 2有',
  `member_discount` decimal(10,2) DEFAULT '0.00' COMMENT '会员优惠金额',
  `member_status` tinyint(4) DEFAULT '0' COMMENT '是否有会员折扣',
  `member_balance` decimal(10,1) DEFAULT '0.0' COMMENT '会员折扣',
  `config_integral` int(11) DEFAULT '0' COMMENT '积分抵扣1元对应多少积分',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `uniacid` (`uniacid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1403 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_list`
--

LOCK TABLES `ims_massage_service_order_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_list_data`
--

DROP TABLE IF EXISTS `ims_massage_service_order_list_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_list_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT '0',
  `sign_time` bigint(11) DEFAULT '0' COMMENT '签字时间',
  `sign_img` varchar(255) DEFAULT '' COMMENT '签字图片',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1008 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_list_data`
--

LOCK TABLES `ims_massage_service_order_list_data` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_list_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_list_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_order_price_log`
--

DROP TABLE IF EXISTS `ims_massage_service_order_price_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_order_price_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_price` decimal(10,2) DEFAULT '0.00',
  `can_refund_price` decimal(10,2) DEFAULT '0.00',
  `is_top` int(11) DEFAULT NULL,
  `top_order_id` int(11) DEFAULT '0',
  `transaction_id` varchar(128) DEFAULT '',
  `order_code` varchar(128) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1402 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_order_price_log`
--

LOCK TABLES `ims_massage_service_order_price_log` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_order_price_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_order_price_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_printer`
--

DROP TABLE IF EXISTS `ims_massage_service_printer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_printer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT '0' COMMENT '门店id',
  `user` varchar(64) DEFAULT '0' COMMENT '用户id',
  `code` varchar(64) DEFAULT '0' COMMENT '终端号',
  `api_key` varchar(64) DEFAULT '0' COMMENT 'api_key',
  `printer_key` varchar(64) DEFAULT '0' COMMENT '打印机key',
  `type` int(11) DEFAULT '1' COMMENT '1:飞蛾 2:易联云',
  `user_ticket` int(11) DEFAULT '0' COMMENT '用户小票',
  `user_ticket_num` int(11) DEFAULT '1' COMMENT '用户小票联数',
  `kitchen_ticket` int(11) DEFAULT '0' COMMENT '后厨小票',
  `kitchen_ticket_num` int(11) DEFAULT '1' COMMENT '后厨小票联数',
  `status` int(11) DEFAULT '1',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `auto` int(11) DEFAULT '1' COMMENT '自动打印',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_printer`
--

LOCK TABLES `ims_massage_service_printer` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_printer` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_printer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_refund_order`
--

DROP TABLE IF EXISTS `ims_massage_service_refund_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_refund_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_code` varchar(64) DEFAULT '',
  `order_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `transaction_id` varchar(64) DEFAULT '',
  `coach_id` int(11) DEFAULT '0' COMMENT '教练',
  `pay_price` double(10,2) DEFAULT '0.00',
  `apply_price` double(10,2) DEFAULT '0.00',
  `refund_price` double(10,2) DEFAULT '0.00',
  `status` int(11) DEFAULT '1',
  `text` varchar(625) CHARACTER SET utf8mb4 DEFAULT '',
  `refund_text` varchar(625) DEFAULT '',
  `create_time` bigint(12) DEFAULT '0',
  `refund_time` bigint(12) DEFAULT '0',
  `balance` double(10,2) DEFAULT '0.00',
  `cancel_time` bigint(11) DEFAULT '0',
  `out_refund_no` varchar(64) DEFAULT '',
  `imgs` varchar(1024) DEFAULT '',
  `car_price` double(10,2) DEFAULT '0.00',
  `time_long` int(11) DEFAULT '0' COMMENT '退款服务时长',
  `service_price` double(10,2) DEFAULT '0.00',
  `admin_id` int(11) DEFAULT '0',
  `is_add` tinyint(3) DEFAULT '0' COMMENT '是否是加钟订单',
  `partner_id` int(11) DEFAULT '0' COMMENT '合伙人id',
  `is_red` tinyint(3) DEFAULT '1' COMMENT '是否展示小气泡 1是 0已看',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=491 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_refund_order`
--

LOCK TABLES `ims_massage_service_refund_order` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_refund_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_refund_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_refund_order_goods`
--

DROP TABLE IF EXISTS `ims_massage_service_refund_order_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_refund_order_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `refund_id` int(11) DEFAULT '0',
  `goods_id` int(11) DEFAULT '0',
  `goods_name` varchar(255) DEFAULT '',
  `goods_cover` varchar(255) DEFAULT '',
  `goods_price` decimal(10,2) DEFAULT '0.00',
  `num` int(11) DEFAULT '1',
  `order_goods_id` int(11) DEFAULT '0',
  `order_id` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=492 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_refund_order_goods`
--

LOCK TABLES `ims_massage_service_refund_order_goods` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_refund_order_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_refund_order_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_service_coach`
--

DROP TABLE IF EXISTS `ims_massage_service_service_coach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_service_coach` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `ser_id` int(11) DEFAULT '0' COMMENT '服务id',
  `coach_id` int(11) DEFAULT '0' COMMENT '教练id',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '单独设置的价格',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '单独设置的比例',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3911 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_service_coach`
--

LOCK TABLES `ims_massage_service_service_coach` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_service_coach` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_service_coach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_service_coach_bak`
--

DROP TABLE IF EXISTS `ims_massage_service_service_coach_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_service_coach_bak` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `ser_id` int(11) DEFAULT '0' COMMENT '服务id',
  `coach_id` int(11) DEFAULT '0' COMMENT '教练id',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '单独设置的价格',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '单独设置的比例',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=398 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_service_coach_bak`
--

LOCK TABLES `ims_massage_service_service_coach_bak` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_service_coach_bak` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_service_coach_bak` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_service_list`
--

DROP TABLE IF EXISTS `ims_massage_service_service_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_service_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `cover` varchar(255) DEFAULT '' COMMENT '封面图',
  `price` double(10,2) DEFAULT '0.00' COMMENT '价格',
  `sale` int(11) DEFAULT '0' COMMENT '销量',
  `true_sale` int(11) DEFAULT '0' COMMENT '实际销量',
  `total_sale` int(11) DEFAULT '0' COMMENT '总销量',
  `time_long` int(11) DEFAULT '0' COMMENT '服务时长',
  `max_time` int(11) DEFAULT '0' COMMENT '最长预约',
  `introduce` text COMMENT '介绍',
  `explain` text COMMENT '说明',
  `notice` text COMMENT '须知',
  `top` int(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  `star` double(10,2) DEFAULT '5.00',
  `imgs` text,
  `lock` int(11) DEFAULT '0',
  `init_price` double(10,2) DEFAULT '0.00' COMMENT '原价',
  `com_balance` int(11) DEFAULT '0' COMMENT '分销比例',
  `sub_title` varchar(255) DEFAULT '',
  `is_add` tinyint(3) DEFAULT '0' COMMENT '是否是加钟服务',
  `admin_id` int(11) DEFAULT '11',
  `check_time` bigint(11) DEFAULT '0',
  `check_text` varchar(625) CHARACTER SET utf8mb4 DEFAULT '',
  `type` tinyint(3) DEFAULT '1',
  `check_status` tinyint(3) DEFAULT '2',
  `min_num` int(11) DEFAULT '1' COMMENT '起购数',
  `is_car` tinyint(3) DEFAULT '1' COMMENT '是否计算车费  1开启 0关闭',
  `service_type` int(11) DEFAULT '0' COMMENT '服务分类id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_service_list`
--

LOCK TABLES `ims_massage_service_service_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_service_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_service_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_service_type_list`
--

DROP TABLE IF EXISTS `ims_massage_service_service_type_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_service_type_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '' COMMENT '服务类型名称',
  `top` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态 1开启 0关闭',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='服务分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_service_type_list`
--

LOCK TABLES `ims_massage_service_service_type_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_service_type_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_service_type_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_shop_carte`
--

DROP TABLE IF EXISTS `ims_massage_service_shop_carte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_shop_carte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态 1上架 0下架 -1 删除',
  `sort` int(10) DEFAULT NULL COMMENT '排序值  倒序',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='物料分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_shop_carte`
--

LOCK TABLES `ims_massage_service_shop_carte` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_shop_carte` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_shop_carte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_shop_goods`
--

DROP TABLE IF EXISTS `ims_massage_service_shop_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_shop_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `carte` varchar(255) DEFAULT '' COMMENT '分类 以，分割',
  `cover` varchar(255) DEFAULT '' COMMENT '封面图',
  `images` text COMMENT '轮播图 json',
  `image_url` varchar(255) DEFAULT NULL COMMENT '轮播跳转链接',
  `video_url` varchar(255) DEFAULT NULL COMMENT '视频地址',
  `desc` longtext COMMENT '详情',
  `phone` varchar(255) DEFAULT NULL COMMENT '平台手机号',
  `uniacid` int(11) DEFAULT NULL,
  `status` tinyint(3) DEFAULT '1' COMMENT '状态 1上架 0下架 -1 删除',
  `sort` int(10) DEFAULT NULL COMMENT '排序值  倒序',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='物料商城商品';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_shop_goods`
--

LOCK TABLES `ims_massage_service_shop_goods` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_shop_goods` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_shop_goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_up_order_goods_list`
--

DROP TABLE IF EXISTS `ims_massage_service_up_order_goods_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_up_order_goods_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `goods_name` varchar(255) DEFAULT '',
  `goods_cover` varchar(255) DEFAULT '',
  `price` decimal(10,2) DEFAULT '0.00',
  `true_price` decimal(10,2) DEFAULT '0.00',
  `time_long` int(11) DEFAULT '0',
  `num` int(11) DEFAULT NULL,
  `order_goods_id` int(11) DEFAULT '0',
  `pay_price` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_up_order_goods_list`
--

LOCK TABLES `ims_massage_service_up_order_goods_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_up_order_goods_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_up_order_goods_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_up_order_list`
--

DROP TABLE IF EXISTS `ims_massage_service_up_order_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_up_order_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_code` varchar(64) DEFAULT '',
  `pay_price` decimal(10,2) DEFAULT '0.00',
  `user_id` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  `pay_type` tinyint(3) DEFAULT '1',
  `transaction_id` varchar(64) DEFAULT '',
  `surplus_price` decimal(10,2) DEFAULT '0.00' COMMENT '除去退款剩余金额',
  `pay_model` int(11) DEFAULT '1',
  `order_price` decimal(10,2) DEFAULT '0.00',
  `pay_time` bigint(11) DEFAULT '0',
  `total_num` int(11) DEFAULT '0',
  `order_goods_id` int(11) DEFAULT '0',
  `balance` decimal(10,2) DEFAULT '0.00',
  `coach_id` int(11) DEFAULT '0',
  `time_long` int(11) DEFAULT '0',
  `service_price` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `over_time` int(11) DEFAULT '0',
  `true_service_price` decimal(10,2) DEFAULT '0.00',
  `coupon_discount` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_up_order_list`
--

LOCK TABLES `ims_massage_service_up_order_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_up_order_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_up_order_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_user_label_data`
--

DROP TABLE IF EXISTS `ims_massage_service_user_label_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_user_label_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `label_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `user_id` int(11) DEFAULT '0',
  `create_time` bigint(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_user_label_data`
--

LOCK TABLES `ims_massage_service_user_label_data` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_user_label_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_user_label_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_user_label_list`
--

DROP TABLE IF EXISTS `ims_massage_service_user_label_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_user_label_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT '',
  `status` tinyint(3) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_user_label_list`
--

LOCK TABLES `ims_massage_service_user_label_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_user_label_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_user_label_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_user_list`
--

DROP TABLE IF EXISTS `ims_massage_service_user_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_user_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(64) NOT NULL DEFAULT '',
  `nickName` varchar(255) CHARACTER SET utf8mb4 DEFAULT '',
  `avatarUrl` varchar(255) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `status` tinyint(3) DEFAULT '1',
  `cap_id` int(11) DEFAULT '0',
  `city` varchar(255) DEFAULT '',
  `country` varchar(255) DEFAULT '',
  `gender` int(11) DEFAULT '0',
  `language` varchar(32) DEFAULT '',
  `province` varchar(128) DEFAULT '',
  `balance` double(10,2) DEFAULT '0.00' COMMENT '余额',
  `phone` varchar(32) DEFAULT '',
  `session_key` varchar(255) DEFAULT '',
  `pid` int(11) DEFAULT '0',
  `cash` double(10,2) DEFAULT '0.00' COMMENT '分销佣金',
  `unionid` varchar(64) DEFAULT '',
  `app_openid` varchar(64) DEFAULT '',
  `web_openid` varchar(64) DEFAULT '',
  `wechat_openid` varchar(64) DEFAULT '',
  `last_login_type` tinyint(3) DEFAULT '0' COMMENT '0小程序 1app 2web',
  `new_cash` decimal(10,2) DEFAULT '0.00' COMMENT '新的分销佣金可提现',
  `lock` int(11) DEFAULT '0',
  `is_fx` tinyint(3) DEFAULT '0',
  `ios_openid` varchar(64) DEFAULT '0' COMMENT '苹果登录的账号',
  `push_id` varchar(64) DEFAULT '',
  `alipay_number` varchar(128) DEFAULT '' COMMENT '支付宝账号',
  `alipay_name` varchar(128) CHARACTER SET utf8mb4 DEFAULT '',
  `is_blacklist` tinyint(3) DEFAULT '0' COMMENT '是否黑名单 1是 0否',
  `bind_time` int(10) DEFAULT '0' COMMENT '绑定分销商时间',
  `package_cash` decimal(10,2) DEFAULT '0.00' COMMENT '门店套餐分销金额',
  `total_package_cash` decimal(10,2) DEFAULT '0.00' COMMENT '总门店套餐分销金额',
  `total_integral` int(10) DEFAULT '0' COMMENT '总积分',
  `integral` int(10) DEFAULT '0' COMMENT '剩余积分',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `openid` (`openid`,`uniacid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=635 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_user_list`
--

LOCK TABLES `ims_massage_service_user_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_user_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_user_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_service_wallet_list`
--

DROP TABLE IF EXISTS `ims_massage_service_wallet_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_service_wallet_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT '',
  `user_id` int(11) DEFAULT '0',
  `coach_id` int(11) DEFAULT NULL,
  `total_price` double(10,2) DEFAULT '0.00' COMMENT '总代提现多少',
  `apply_price` double(10,2) DEFAULT '0.00',
  `service_price` double(10,2) DEFAULT '0.00' COMMENT '手续费',
  `balance` int(11) DEFAULT '0' COMMENT '提成比例',
  `true_price` double(10,2) DEFAULT '0.00' COMMENT '实际到账',
  `status` int(11) DEFAULT '1',
  `create_time` bigint(11) DEFAULT '11',
  `sh_time` bigint(11) DEFAULT '0',
  `type` tinyint(3) DEFAULT '0' COMMENT '1是车费',
  `online` tinyint(3) DEFAULT '0',
  `payment_no` varchar(64) DEFAULT '',
  `text` varchar(1024) DEFAULT '',
  `admin_id` int(11) DEFAULT '0',
  `apply_transfer` tinyint(3) DEFAULT '0' COMMENT '申请转账方式',
  `lock` int(11) DEFAULT '0',
  `user_num` varchar(225) DEFAULT '',
  `tax_point` int(11) DEFAULT '0' COMMENT '税点',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_service_wallet_list`
--

LOCK TABLES `ims_massage_service_wallet_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_service_wallet_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_service_wallet_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_shield_list`
--

DROP TABLE IF EXISTS `ims_massage_shield_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_shield_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `coach_id` int(11) DEFAULT NULL,
  `type` tinyint(3) DEFAULT '1' COMMENT '1动态  2技师',
  `create_time` bigint(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_shield_list`
--

LOCK TABLES `ims_massage_shield_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_shield_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_shield_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_short_code_config`
--

DROP TABLE IF EXISTS `ims_massage_short_code_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_short_code_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `short_sign` varchar(255) DEFAULT '' COMMENT '短信签名',
  `order_short_code` varchar(255) DEFAULT '' COMMENT '订单短信模版code',
  `refund_short_code` varchar(255) DEFAULT NULL COMMENT '退单短信模版code',
  `help_short_code` varchar(255) DEFAULT '',
  `short_code` varchar(255) DEFAULT NULL,
  `short_code_status` tinyint(3) DEFAULT '0',
  `moor_order_short_code` varchar(255) DEFAULT '',
  `moor_refund_short_code` varchar(255) DEFAULT '',
  `moor_help_short_code` varchar(255) DEFAULT '',
  `moor_short_code` varchar(255) DEFAULT '',
  `type` tinyint(3) DEFAULT '1' COMMENT '1阿里云 2七莫',
  `bind_phone_type` tinyint(3) DEFAULT '0' COMMENT '绑定手机号方式，默认',
  `winner_order_text` varchar(255) DEFAULT '' COMMENT '云信下单通知内容',
  `winner_refund_text` varchar(255) DEFAULT '' COMMENT '云信退款通知内容',
  `winner_police_text` varchar(255) DEFAULT '' COMMENT '云信求救通知内容',
  `winner_code_text` varchar(255) DEFAULT '' COMMENT '云信验证码通知内容',
  `winner_user` varchar(255) DEFAULT '' COMMENT '云信短信登录账号',
  `winner_pass` varchar(255) DEFAULT '' COMMENT '云信短信登录密码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_short_code_config`
--

LOCK TABLES `ims_massage_short_code_config` WRITE;
/*!40000 ALTER TABLE `ims_massage_short_code_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_short_code_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_apply`
--

DROP TABLE IF EXISTS `ims_massage_store_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `name` varchar(64) DEFAULT '' COMMENT '名称',
  `cover` varchar(255) DEFAULT '' COMMENT '封面',
  `banner` text COMMENT '轮播 ,分割',
  `mobile` varchar(16) DEFAULT '' COMMENT '手机号',
  `license` varchar(255) DEFAULT '' COMMENT '执照',
  `lng` varchar(64) DEFAULT '' COMMENT '经度',
  `lat` varchar(64) DEFAULT '' COMMENT '维度',
  `address` varchar(255) DEFAULT '' COMMENT '地址',
  `info` varchar(255) DEFAULT '' COMMENT '门牌号',
  `tag` text COMMENT '标签 ,分割',
  `status` tinyint(3) DEFAULT '1' COMMENT '-1 删除 1待审核 2审核通过 3拒绝',
  `intro` text COMMENT '介绍',
  `is_update` tinyint(3) DEFAULT '0' COMMENT '是否有修改',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `check_time` int(11) DEFAULT '0' COMMENT '审核时间',
  `check_msg` varchar(1024) DEFAULT '' COMMENT '审核理由',
  `type_id` varchar(255) DEFAULT '' COMMENT '分类id',
  `is_admin` int(10) DEFAULT '0' COMMENT '是否后台添加',
  `top` int(10) DEFAULT '0' COMMENT '排序',
  `is_top` int(10) DEFAULT '0' COMMENT '是否置顶',
  `star` decimal(10,1) DEFAULT '5.0' COMMENT '评分',
  `store_balance` decimal(10,2) DEFAULT '0.00' COMMENT '门店比例',
  `share_balance` decimal(10,2) DEFAULT '0.00' COMMENT '推广者比例',
  `cash` decimal(10,2) DEFAULT '0.00' COMMENT '分销金额',
  `total_cash` decimal(10,2) DEFAULT '0.00' COMMENT '分销总金额',
  `trade_week` varchar(255) DEFAULT '1,2,3,4,5,6,0' COMMENT '营业时间周',
  `start_time` varchar(255) DEFAULT '19:00' COMMENT '开始时间',
  `end_time` varchar(255) DEFAULT '05:00' COMMENT '结束时间',
  `contact_type` tinyint(4) DEFAULT '1' COMMENT '联系方式 1手机号 2企业微信',
  `qywx_kid` varchar(255) DEFAULT '' COMMENT '企业微信',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='商家入驻';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_apply`
--

LOCK TABLES `ims_massage_store_apply` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_apply` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_apply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_apply_update`
--

DROP TABLE IF EXISTS `ims_massage_store_apply_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_apply_update` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT '0' COMMENT '门店id',
  `uniacid` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `name` varchar(64) DEFAULT '' COMMENT '名称',
  `cover` varchar(255) DEFAULT '' COMMENT '封面',
  `banner` text COMMENT '轮播 ,分割',
  `mobile` varchar(16) DEFAULT '' COMMENT '手机号',
  `license` varchar(255) DEFAULT '' COMMENT '执照',
  `lng` varchar(64) DEFAULT '' COMMENT '经度',
  `lat` varchar(64) DEFAULT '' COMMENT '维度',
  `address` varchar(255) DEFAULT '' COMMENT '地址',
  `info` varchar(255) DEFAULT '' COMMENT '门牌号',
  `tag` text COMMENT '标签 ,分割',
  `status` tinyint(3) DEFAULT '1' COMMENT '1待审核 2通过 3拒绝',
  `intro` text COMMENT '介绍',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `check_time` int(11) DEFAULT '0' COMMENT '审核时间',
  `check_msg` varchar(1024) DEFAULT '' COMMENT '审核理由',
  `type_id` varchar(255) DEFAULT '' COMMENT '分类id',
  `trade_week` varchar(255) DEFAULT '' COMMENT '营业时间周',
  `start_time` varchar(255) DEFAULT '' COMMENT '开始时间',
  `end_time` varchar(255) DEFAULT '' COMMENT '结束时间',
  `contact_type` tinyint(4) DEFAULT '1' COMMENT '联系方式 1手机号 2企业微信',
  `qywx_kid` varchar(255) DEFAULT '' COMMENT '企业微信',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='商家入驻修改';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_apply_update`
--

LOCK TABLES `ims_massage_store_apply_update` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_apply_update` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_apply_update` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_list`
--

DROP TABLE IF EXISTS `ims_massage_store_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(64) DEFAULT '',
  `attestation` varchar(255) DEFAULT '' COMMENT '门店认证',
  `star` decimal(10,1) DEFAULT '0.0' COMMENT '评分',
  `order_num` int(11) DEFAULT '0' COMMENT '虚拟单量',
  `order_rate` int(11) DEFAULT '0' COMMENT '接单率',
  `true_order_num` int(11) DEFAULT '0' COMMENT '真实单量',
  `total_num` int(11) DEFAULT '0' COMMENT '总单量',
  `positive_rate` int(11) DEFAULT '0' COMMENT '好评率',
  `business_license` varchar(255) DEFAULT '' COMMENT '营业执照',
  `text` text CHARACTER SET utf8mb4,
  `admin_id` int(11) DEFAULT '0' COMMENT '代理商',
  `start_time` varchar(32) DEFAULT '',
  `end_time` varchar(32) DEFAULT '',
  `lng` varchar(32) DEFAULT '',
  `lat` varchar(32) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `create_time` bigint(11) DEFAULT '0',
  `status` int(11) DEFAULT '1',
  `sh_time` bigint(11) DEFAULT '0',
  `sh_text` varchar(1024) CHARACTER SET utf8mb4 DEFAULT '',
  `cover` varchar(225) DEFAULT '',
  `phone` varchar(32) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_list`
--

LOCK TABLES `ims_massage_store_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_list`
--

DROP TABLE IF EXISTS `ims_massage_store_package_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '门店id',
  `status` int(10) DEFAULT '1' COMMENT '状态',
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `sub_name` varchar(255) DEFAULT '' COMMENT '副标题',
  `cover` varchar(255) DEFAULT '' COMMENT '封面图',
  `price` double(10,1) DEFAULT '0.0' COMMENT '现价',
  `init_price` double(10,1) DEFAULT '0.0' COMMENT '划线价',
  `sale` int(11) DEFAULT '0' COMMENT '销量',
  `true_sale` int(11) DEFAULT '0' COMMENT '实际销量',
  `total_sale` int(11) DEFAULT '0' COMMENT '总销量',
  `imgs` text COMMENT '使用规则',
  `introduce` text COMMENT '介绍',
  `term_type` tinyint(3) DEFAULT '1' COMMENT '有效期类型 1使用时间 2有效时间',
  `term_start_time` int(10) DEFAULT '0' COMMENT '开始时间',
  `term_end_time` int(10) DEFAULT '0' COMMENT '结束时间',
  `days` int(10) DEFAULT '0' COMMENT '有效天数',
  `use_type` tinyint(3) DEFAULT '1' COMMENT '使用时间类型 1与门店一致  2自定义时间',
  `use_trade_week` varchar(255) DEFAULT '' COMMENT '营业时间周',
  `use_start_time` varchar(255) DEFAULT '' COMMENT '开始时间',
  `use_end_time` varchar(255) DEFAULT '' COMMENT '结束时间',
  `reservation_day` int(10) DEFAULT '0' COMMENT '预约天数 0为无需预约',
  `rule_text` text COMMENT '使用规则',
  `ensure` tinyint(3) DEFAULT '1' COMMENT '保障 1随时退 2人工退',
  `is_admin` tinyint(3) DEFAULT '1' COMMENT '是否为后台增加 1是',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `lock` int(11) DEFAULT '0' COMMENT '锁',
  `introduce_text` text COMMENT '数组',
  `is_integral` tinyint(4) DEFAULT '0' COMMENT '是否开启积分兑换 1是',
  `integral` int(11) DEFAULT '0' COMMENT '使用的积分',
  `integral_to_money` decimal(10,1) DEFAULT '0.0' COMMENT '使用积分兑换的金额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='门店套餐列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_list`
--

LOCK TABLES `ims_massage_store_package_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_order_comment_list`
--

DROP TABLE IF EXISTS `ims_massage_store_package_order_comment_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_order_comment_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '门店id',
  `star` char(4) NOT NULL DEFAULT '0' COMMENT '星级',
  `text` varchar(1024) DEFAULT '' COMMENT '评论',
  `img` varchar(1024) DEFAULT '' COMMENT '图片',
  `is_hide` tinyint(3) DEFAULT '0' COMMENT '是否匿名 1是',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(3) DEFAULT '1' COMMENT '状态',
  `is_admin` tinyint(3) DEFAULT '0' COMMENT '类型 1后台 0用户',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='门店套餐订单评价';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_order_comment_list`
--

LOCK TABLES `ims_massage_store_package_order_comment_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_order_comment_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_order_comment_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_order_goods_list`
--

DROP TABLE IF EXISTS `ims_massage_store_package_order_goods_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_order_goods_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `code_num` varchar(255) DEFAULT '' COMMENT '编号',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `status` int(11) DEFAULT '1' COMMENT '1待使用 2已使用 3已退货',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `is_integral` tinyint(4) DEFAULT '0' COMMENT '是否使用积分兑换 1是',
  `integral` int(11) DEFAULT '0' COMMENT '使用的积分',
  `integral_to_money` decimal(10,1) DEFAULT '0.0' COMMENT '使用积分兑换的金额',
  `goods_price` decimal(10,2) DEFAULT '0.00' COMMENT '套餐单价',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=458 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='套餐订单下级';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_order_goods_list`
--

LOCK TABLES `ims_massage_store_package_order_goods_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_order_goods_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_order_goods_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_order_hx_list`
--

DROP TABLE IF EXISTS `ims_massage_store_package_order_hx_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_order_hx_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `code_num` varchar(255) DEFAULT '' COMMENT '编号 ,分割',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '申请数量',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='套餐订单核销记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_order_hx_list`
--

LOCK TABLES `ims_massage_store_package_order_hx_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_order_hx_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_order_hx_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_order_list`
--

DROP TABLE IF EXISTS `ims_massage_store_package_order_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_order_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '门店id',
  `order_code` varchar(255) NOT NULL DEFAULT '' COMMENT '订单号',
  `status` tinyint(3) DEFAULT '1' COMMENT '订单状态 -1取消 1待支付 2待使用 3已完成',
  `mobile` varchar(255) DEFAULT '' COMMENT '手机号',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '商户订单号',
  `pay_price` decimal(10,2) DEFAULT '0.00' COMMENT '支付金额',
  `pay_model` tinyint(3) DEFAULT '1' COMMENT '支付方式 1微信 2余额 3支付宝',
  `pay_time` int(11) DEFAULT '0' COMMENT '支付时间',
  `package_price` decimal(10,2) DEFAULT '0.00' COMMENT '套餐总金额',
  `true_package_price` decimal(10,2) DEFAULT '0.00' COMMENT '套餐实际总金额',
  `discount_price` decimal(10,2) DEFAULT '0.00' COMMENT '优惠金额',
  `num` int(11) DEFAULT '0' COMMENT '商品数量',
  `can_refund_num` int(11) DEFAULT '0' COMMENT '可退货商品数量',
  `start_time` bigint(12) DEFAULT '0' COMMENT '开始时间',
  `end_time` bigint(12) DEFAULT '0' COMMENT '结束时间',
  `hx_time` int(11) DEFAULT '0' COMMENT '核销时间',
  `over_time` int(11) DEFAULT '0' COMMENT '取消订单时间',
  `is_del` tinyint(3) DEFAULT '0' COMMENT '是否删除 1是',
  `qr_path` varchar(255) DEFAULT '' COMMENT '核销二维码',
  `share_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '推广人比例',
  `store_balance` decimal(10,2) DEFAULT '0.00' COMMENT '门店比例',
  `share_balance` decimal(10,2) DEFAULT '0.00' COMMENT '推广人比例',
  `store_cash` decimal(10,2) DEFAULT '0.00' COMMENT '门店佣金',
  `share_cash` decimal(10,2) DEFAULT '0.00' COMMENT '推广币比例',
  `company_cash` decimal(10,2) DEFAULT '0.00' COMMENT '平台佣金',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '套餐id',
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `sub_name` varchar(255) DEFAULT '' COMMENT '副标题',
  `cover` varchar(255) DEFAULT '' COMMENT '封面图',
  `price` double(10,1) DEFAULT '0.0' COMMENT '现价',
  `init_price` double(10,1) DEFAULT '0.0' COMMENT '划线价',
  `use_start_time` varchar(255) DEFAULT '' COMMENT '开始时间',
  `use_end_time` varchar(255) DEFAULT '' COMMENT '结束时间',
  `reservation_day` int(10) DEFAULT '0' COMMENT '预约天数 0为无需预约',
  `rule_text` text COMMENT '使用规则',
  `ensure` tinyint(3) DEFAULT '1' COMMENT '保障 1随时退 2人工退',
  `sku` text COMMENT '规格',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `is_comment` tinyint(3) DEFAULT '0' COMMENT '是否评价 1是',
  `consume_time` bigint(12) DEFAULT '0' COMMENT '消费时间 多个每次使用更新',
  `is_refund` tinyint(3) DEFAULT '0' COMMENT '是否正在售后 1是',
  `refund_price` decimal(10,2) DEFAULT '0.00' COMMENT '已退款成功金额',
  `out_refund_code` varchar(255) DEFAULT '' COMMENT '外部退款编号',
  `hx_num` int(11) DEFAULT '0' COMMENT '核销数量',
  `app_pay` int(10) DEFAULT '0' COMMENT '是否app',
  `is_seckill` int(10) DEFAULT '0' COMMENT '是否是秒杀 1是',
  `seckill_id` int(10) DEFAULT '0' COMMENT '秒杀id',
  `seckill_end_time` bigint(10) DEFAULT '0' COMMENT '秒杀结束时间',
  `is_integral` tinyint(4) DEFAULT '0' COMMENT '是否使用积分兑换 1是',
  `integral` int(11) DEFAULT '0' COMMENT '使用的积分',
  `integral_to_money` decimal(10,1) DEFAULT '0.0' COMMENT '使用积分兑换的金额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=338 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='套餐订单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_order_list`
--

LOCK TABLES `ims_massage_store_package_order_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_order_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_order_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_order_refund_goods_list`
--

DROP TABLE IF EXISTS `ims_massage_store_package_order_refund_goods_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_order_refund_goods_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `code_num` varchar(255) DEFAULT '' COMMENT '编号',
  `refund_id` int(11) NOT NULL DEFAULT '0' COMMENT '退款订单id',
  `order_goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '原订单下级id',
  `status` int(11) DEFAULT '1' COMMENT '1申请中 2已退款 3拒绝',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='套餐退款订单下级';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_order_refund_goods_list`
--

LOCK TABLES `ims_massage_store_package_order_refund_goods_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_order_refund_goods_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_order_refund_goods_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_order_refund_list`
--

DROP TABLE IF EXISTS `ims_massage_store_package_order_refund_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_order_refund_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '订单id',
  `store_id` int(11) NOT NULL DEFAULT '0' COMMENT '门店id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `refund_code` varchar(255) DEFAULT '' COMMENT '退款编号',
  `out_refund_code` varchar(255) DEFAULT '' COMMENT '外部退款编号',
  `status` int(11) DEFAULT '1' COMMENT '1申请中 2已退款 3拒绝',
  `apply_price` decimal(10,2) DEFAULT '0.00' COMMENT '申请金额',
  `refund_price` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `text` varchar(625) DEFAULT '' COMMENT '申请理由',
  `refund_text` varchar(625) DEFAULT '' COMMENT '审核理由',
  `refund_time` int(11) NOT NULL DEFAULT '0' COMMENT '退款时间',
  `refund_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '退款方式 1自动退款 2人工退款',
  `apply_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '申请方式 1人工 2系统',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '套餐id',
  `pay_model` tinyint(3) DEFAULT '1' COMMENT '支付方式 1微信 2余额 3支付宝',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '申请数量',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `is_del` tinyint(3) DEFAULT '0' COMMENT '是否删除 1是',
  `integral` int(11) DEFAULT '0' COMMENT '应退积分',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='套餐退款订单下级';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_order_refund_list`
--

LOCK TABLES `ims_massage_store_package_order_refund_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_order_refund_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_order_refund_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_seckill_list`
--

DROP TABLE IF EXISTS `ims_massage_store_package_seckill_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_seckill_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `store_id` int(11) DEFAULT '0' COMMENT '门店id',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '套餐id',
  `status` int(11) DEFAULT '1',
  `stock` int(11) DEFAULT '0' COMMENT '库存数量',
  `use_stock` int(11) DEFAULT '0' COMMENT '已使用库存数量',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '单价',
  `start_time` bigint(11) NOT NULL DEFAULT '0',
  `end_time` bigint(11) NOT NULL DEFAULT '0',
  `limit` int(11) DEFAULT '0' COMMENT '限购数量',
  `is_ad` tinyint(3) DEFAULT '0' COMMENT '是否为广告 1是',
  `create_time` bigint(11) NOT NULL DEFAULT '0',
  `update_time` bigint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=309 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='套餐秒杀表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_seckill_list`
--

LOCK TABLES `ims_massage_store_package_seckill_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_seckill_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_seckill_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_sku`
--

DROP TABLE IF EXISTS `ims_massage_store_package_sku`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_sku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '套餐id',
  `status` int(10) DEFAULT '1' COMMENT '状态',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1120 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='门店套餐sku列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_sku`
--

LOCK TABLES `ims_massage_store_package_sku` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_sku` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_sku` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_package_sku_price`
--

DROP TABLE IF EXISTS `ims_massage_store_package_sku_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_package_sku_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `package_id` int(11) DEFAULT '0' COMMENT '套餐id',
  `sku_id` int(11) DEFAULT '0' COMMENT 'sku id',
  `num` int(11) DEFAULT '0' COMMENT '数量',
  `price` double(10,1) DEFAULT '0.0' COMMENT '价格',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1602 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='门店套餐sku价格列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_package_sku_price`
--

LOCK TABLES `ims_massage_store_package_sku_price` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_package_sku_price` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_package_sku_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_service`
--

DROP TABLE IF EXISTS `ims_massage_store_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_service` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT '0',
  `store_id` int(11) DEFAULT '0',
  `admin_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_service`
--

LOCK TABLES `ims_massage_store_service` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_store_type_list`
--

DROP TABLE IF EXISTS `ims_massage_store_type_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_store_type_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `img` varchar(255) DEFAULT '' COMMENT '图标',
  `top` int(10) DEFAULT '0' COMMENT '排序',
  `status` int(10) DEFAULT '1' COMMENT '状态',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='门店分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_store_type_list`
--

LOCK TABLES `ims_massage_store_type_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_store_type_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_store_type_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_user_channel`
--

DROP TABLE IF EXISTS `ims_massage_user_channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_user_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道商id',
  `channel_staff_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道商员工id',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `all_channel_time` int(11) DEFAULT '0' COMMENT '全局时效性/小时',
  `channel_time` int(11) DEFAULT '0' COMMENT '渠道商时效性/小时',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户-渠道商记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_user_channel`
--

LOCK TABLES `ims_massage_user_channel` WRITE;
/*!40000 ALTER TABLE `ims_massage_user_channel` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_user_channel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_user_from_list`
--

DROP TABLE IF EXISTS `ims_massage_user_from_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_user_from_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `from_id` int(11) NOT NULL DEFAULT '0' COMMENT '来源ID 1无id 2用户id 3分销员id 4渠道商id 5员工id 6经纪人id 7代理商id',
  `from_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '来源类型 1公众号搜索 2分享链接 3分销码 4渠道码 5渠道员工码 6技师邀请码 7代理商邀请码',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `from_name` varchar(255) DEFAULT '' COMMENT '达人账号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=522 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户来源';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_user_from_list`
--

LOCK TABLES `ims_massage_user_from_list` WRITE;
/*!40000 ALTER TABLE `ims_massage_user_from_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_user_from_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_user_store_package_collect`
--

DROP TABLE IF EXISTS `ims_massage_user_store_package_collect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_user_store_package_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '套餐id',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户门店套餐收藏';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_user_store_package_collect`
--

LOCK TABLES `ims_massage_user_store_package_collect` WRITE;
/*!40000 ALTER TABLE `ims_massage_user_store_package_collect` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_user_store_package_collect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_massage_wx_upload`
--

DROP TABLE IF EXISTS `ims_massage_wx_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_massage_wx_upload` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `key` varchar(625) DEFAULT '' COMMENT '密钥',
  `version` varchar(32) DEFAULT '' COMMENT '版本号',
  `content` varchar(1024) DEFAULT '' COMMENT '描述',
  `app_id` varchar(1024) DEFAULT '' COMMENT 'app_id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信上传配置';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_massage_wx_upload`
--

LOCK TABLES `ims_massage_wx_upload` WRITE;
/*!40000 ALTER TABLE `ims_massage_wx_upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_massage_wx_upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_shequshop_school_admin`
--

DROP TABLE IF EXISTS `ims_shequshop_school_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_shequshop_school_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT '',
  `passwd` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `is_admin` tinyint(3) DEFAULT '1' COMMENT '是否是超管',
  `balance` int(11) DEFAULT '0' COMMENT '分佣比例',
  `user_id` int(11) DEFAULT '0' COMMENT '绑定用户',
  `status` tinyint(3) DEFAULT '1',
  `passwd_text` varchar(255) DEFAULT '',
  `cash` double(10,2) DEFAULT '0.00' COMMENT '佣金',
  `lock` int(11) DEFAULT '0',
  `phone` varchar(32) DEFAULT '',
  `city_type` tinyint(3) DEFAULT '1' COMMENT '1城市代理 2区县代理',
  `admin_pid` int(11) DEFAULT '0' COMMENT '上级代理商',
  `level_balance` int(11) DEFAULT '0' COMMENT '上级',
  `city_id` int(11) DEFAULT '0' COMMENT '城市id',
  `province` varchar(255) DEFAULT '',
  `agent_name` varchar(255) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '代理商名字',
  `agreement` varchar(255) DEFAULT '' COMMENT '公司合同模版',
  `agreement_title` varchar(255) DEFAULT '' COMMENT '合同标题',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_shequshop_school_admin`
--

LOCK TABLES `ims_shequshop_school_admin` WRITE;
/*!40000 ALTER TABLE `ims_shequshop_school_admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_shequshop_school_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_shequshop_school_attachment`
--

DROP TABLE IF EXISTS `ims_shequshop_school_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_shequshop_school_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `module_upload_dir` varchar(100) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `longbing_attachment_path` char(255) NOT NULL DEFAULT '' COMMENT 'path',
  `longbing_driver` char(10) NOT NULL DEFAULT '' COMMENT 'loacl',
  `longbing_from` varchar(255) NOT NULL DEFAULT '' COMMENT 'web',
  `admin_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2797 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_shequshop_school_attachment`
--

LOCK TABLES `ims_shequshop_school_attachment` WRITE;
/*!40000 ALTER TABLE `ims_shequshop_school_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_shequshop_school_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_shequshop_school_attachment_group`
--

DROP TABLE IF EXISTS `ims_shequshop_school_attachment_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_shequshop_school_attachment_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT '0',
  `type` tinyint(1) DEFAULT '0',
  `admin_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_shequshop_school_attachment_group`
--

LOCK TABLES `ims_shequshop_school_attachment_group` WRITE;
/*!40000 ALTER TABLE `ims_shequshop_school_attachment_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_shequshop_school_attachment_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_shequshop_school_config`
--

DROP TABLE IF EXISTS `ims_shequshop_school_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_shequshop_school_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `appid` varchar(32) DEFAULT '',
  `appsecret` varchar(64) DEFAULT '',
  `app_name` varchar(255) DEFAULT '',
  `over_time` int(11) DEFAULT '300' COMMENT '订单超时取消',
  `cash_balance` int(11) DEFAULT '100' COMMENT ' 提现比列',
  `min_cash` double(10,2) DEFAULT '0.00' COMMENT '最低提现金额',
  `gzh_appid` varchar(64) DEFAULT '',
  `order_tmp_id` varchar(128) DEFAULT '' COMMENT '下单通知',
  `cancel_tmp_id` varchar(128) DEFAULT '',
  `max_day` int(11) DEFAULT '3' COMMENT '最长预约时间',
  `time_unit` int(11) DEFAULT '30' COMMENT '时长单位',
  `service_cover_time` int(11) DEFAULT '0' COMMENT '服务倒计时',
  `can_tx_time` int(11) DEFAULT '24' COMMENT '多少小时后可提现',
  `cash_mini` double(10,2) DEFAULT '0.01',
  `mobile` varchar(255) DEFAULT '',
  `im_type` int(11) DEFAULT '1',
  `app_app_secret` varchar(64) DEFAULT '',
  `app_app_id` varchar(64) DEFAULT '',
  `login_protocol` text CHARACTER SET utf8mb4,
  `web_app_secret` varchar(64) DEFAULT '',
  `web_app_id` varchar(64) DEFAULT '',
  `map_secret` varchar(64) DEFAULT '',
  `company_pay` tinyint(3) DEFAULT '1',
  `short_id` varchar(64) DEFAULT '',
  `short_secret` varchar(64) DEFAULT '',
  `fx_check` tinyint(3) DEFAULT '0' COMMENT '是否开启分销审核',
  `is_bus` tinyint(3) DEFAULT '1' COMMENT '是否支持公共交通',
  `bus_start_time` varchar(32) DEFAULT '',
  `bus_end_time` varchar(32) DEFAULT '',
  `app_text` varchar(64) DEFAULT '' COMMENT 'app名称',
  `app_logo` varchar(255) DEFAULT '' COMMENT 'applogo',
  `record_type` int(11) DEFAULT '1',
  `record_no` varchar(255) DEFAULT '',
  `trading_rules` longtext COMMENT '交易规则',
  `user_image` varchar(255) DEFAULT 'https://lbqny.migugu.com/admin/anmo/mine/bg.png' COMMENT '个人中心背景图',
  `user_font_color` varchar(32) DEFAULT '#ffffff' COMMENT '文字颜色',
  `coach_image` varchar(255) DEFAULT 'https://lbqny.migugu.com/admin/anmo/mine/bg.png' COMMENT '向导端背景图',
  `coach_font_color` varchar(32) DEFAULT '#ffffff' COMMENT '文字颜色',
  `app_banner` text,
  `information_protection` longtext COMMENT '个人信息保护',
  `countdown_voice` varchar(255) DEFAULT '' COMMENT '倒计时语音',
  `primaryColor` varchar(255) DEFAULT '#A40035',
  `subColor` varchar(255) DEFAULT '#F1C06B',
  `time_interval` int(11) DEFAULT '0' COMMENT '时间间隔 单位分钟',
  `anonymous_evaluate` tinyint(3) DEFAULT '0' COMMENT '是否开启匿名评价',
  `service_btn_color` varchar(64) DEFAULT '#282B34',
  `service_font_color` varchar(64) DEFAULT '#EBDDB1',
  `level_cycle` int(11) DEFAULT '0' COMMENT '0不限 1每周 2每月 3每季度 4每年',
  `web_code_img` varchar(255) DEFAULT '',
  `promotion_poster_img` varchar(255) DEFAULT 'https://lbqny.migugu.com/admin/peiwan/fx-share1.png',
  `bind_technician_img` varchar(255) DEFAULT 'https://lbqnyv2.migugu.com/bianzu18.png',
  `is_current` tinyint(3) DEFAULT '2' COMMENT '2上个周期 1本期',
  `agent_article_id` int(11) DEFAULT '0' COMMENT '代理商入住的文章id',
  `is_demand_order_check` tinyint(3) DEFAULT '0',
  `coach_tc_ratio` int(11) DEFAULT '100' COMMENT '陪玩提成比例',
  `index_type` tinyint(3) DEFAULT '1' COMMENT '首页样式',
  `balance_character` varchar(16) DEFAULT '余额' COMMENT '余额显示文案',
  `cash_type` tinyint(3) DEFAULT '1' COMMENT '分销类型 1浮动 2固定',
  `demand_user_balance` decimal(10,2) DEFAULT '0.00' COMMENT '邀约订单分销员比例',
  `refund_notice` longtext COMMENT '退款须知',
  `withdrawal_notice` longtext COMMENT '提现须知',
  `distribution_poster_img` varchar(255) DEFAULT 'https://lbqny.migugu.com/admin/anmo/mine/fx-share.png' COMMENT '下级推广海报',
  `demand_price_check` tinyint(3) DEFAULT '1' COMMENT '邀约订单价格结算 1自定义 2服务类型价格结算',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_shequshop_school_config`
--

LOCK TABLES `ims_shequshop_school_config` WRITE;
/*!40000 ALTER TABLE `ims_shequshop_school_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_shequshop_school_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_shequshop_school_goods_sh_list`
--

DROP TABLE IF EXISTS `ims_shequshop_school_goods_sh_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_shequshop_school_goods_sh_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `sh_id` int(11) DEFAULT '0' COMMENT '审核id',
  `goods_id` int(11) DEFAULT '0',
  `goods_name` varchar(255) DEFAULT '' COMMENT '商品列表',
  `cate_id` int(11) DEFAULT '0' COMMENT '分类id',
  `cover` varchar(255) DEFAULT '' COMMENT '封面图',
  `imgs` text COMMENT '轮播图',
  `text` text,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_shequshop_school_goods_sh_list`
--

LOCK TABLES `ims_shequshop_school_goods_sh_list` WRITE;
/*!40000 ALTER TABLE `ims_shequshop_school_goods_sh_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_shequshop_school_goods_sh_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_shequshop_school_oos_config`
--

DROP TABLE IF EXISTS `ims_shequshop_school_oos_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_shequshop_school_oos_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `miniapp_name` varchar(50) NOT NULL DEFAULT '',
  `open_oss` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-本地 1-阿里云 2-七牛云  3--腾讯云',
  `aliyun_bucket` varchar(255) NOT NULL DEFAULT '' COMMENT '仓库',
  `aliyun_access_key_id` varchar(50) NOT NULL DEFAULT '' COMMENT '阿里云',
  `aliyun_access_key_secret` varchar(100) NOT NULL DEFAULT '' COMMENT '阿里云',
  `aliyun_base_dir` varchar(200) NOT NULL DEFAULT '' COMMENT '图片等资源存储根目录',
  `aliyun_zidinyi_yuming` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义域名',
  `aliyun_endpoint` varchar(255) NOT NULL DEFAULT '',
  `aliyun_rules` text COMMENT '阿里云的规则配置',
  `qiniu_accesskey` varchar(100) NOT NULL DEFAULT '' COMMENT '七牛云秘钥',
  `qiniu_secretkey` varchar(100) NOT NULL DEFAULT '' COMMENT '七牛云秘钥',
  `qiniu_bucket` varchar(50) NOT NULL DEFAULT '' COMMENT '七牛云仓库',
  `qiniu_yuming` varchar(255) NOT NULL DEFAULT '' COMMENT '七牛自定义域名  前面要加http://',
  `qiniu_rules` text COMMENT '七牛的规则配置',
  `tenxunyun_appid` varchar(20) NOT NULL DEFAULT '' COMMENT '腾讯云的appid',
  `tenxunyun_secretid` varchar(50) NOT NULL DEFAULT '' COMMENT '腾讯云secretid',
  `tenxunyun_secretkey` varchar(50) NOT NULL DEFAULT '' COMMENT '腾讯云的配置',
  `tenxunyun_bucket` varchar(50) NOT NULL DEFAULT '' COMMENT '腾讯云图片仓库',
  `tenxunyun_region` varchar(50) NOT NULL DEFAULT '' COMMENT '腾讯云地域',
  `tenxunyun_yuming` varchar(300) NOT NULL DEFAULT '' COMMENT '腾讯云域名',
  `apiclient_cert` varchar(200) NOT NULL DEFAULT '',
  `apiclient_key` varchar(200) NOT NULL DEFAULT '' COMMENT '两个证书文件路径',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_sync` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否同步',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '储蓄名字',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_shequshop_school_oos_config`
--

LOCK TABLES `ims_shequshop_school_oos_config` WRITE;
/*!40000 ALTER TABLE `ims_shequshop_school_oos_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_shequshop_school_oos_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_shequshop_school_pay_config`
--

DROP TABLE IF EXISTS `ims_shequshop_school_pay_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_shequshop_school_pay_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uniacid` int(10) NOT NULL DEFAULT '0' COMMENT '小程序关联id',
  `mch_id` varchar(255) NOT NULL DEFAULT '' COMMENT '商户号',
  `pay_key` varchar(255) NOT NULL DEFAULT '' COMMENT '支付秘钥',
  `cert_path` varchar(255) NOT NULL DEFAULT '' COMMENT '证书',
  `key_path` varchar(255) NOT NULL DEFAULT '' COMMENT '证书',
  `min_price` int(6) NOT NULL DEFAULT '0' COMMENT '最低提现金额',
  `pay_name` varchar(255) NOT NULL DEFAULT 'wechat' COMMENT '支付类型',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `ali_appid` varchar(64) DEFAULT '' COMMENT '支付宝appid',
  `ali_privatekey` text COMMENT '支付宝私钥',
  `ali_publickey` text COMMENT '支付宝公钥',
  `alipay_status` tinyint(3) DEFAULT '0',
  `appCretPublicKey` varchar(255) DEFAULT '' COMMENT '支付宝应用公钥',
  `alipayCretPublicKey` varchar(255) DEFAULT '' COMMENT '支付宝公钥',
  `alipayRootCret` varchar(255) DEFAULT '' COMMENT '支付宝根证书',
  `alipay_type` tinyint(3) DEFAULT '1' COMMENT '1 密钥模式 2证书模式',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_shequshop_school_pay_config`
--

LOCK TABLES `ims_shequshop_school_pay_config` WRITE;
/*!40000 ALTER TABLE `ims_shequshop_school_pay_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_shequshop_school_pay_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_shequshop_school_wechat_code`
--

DROP TABLE IF EXISTS `ims_shequshop_school_wechat_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_shequshop_school_wechat_code` (
  `id` char(32) NOT NULL DEFAULT '',
  `uniacid` int(11) NOT NULL DEFAULT '0' COMMENT 'uniacid',
  `data` text COMMENT '数据',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `deleted` tinyint(1) DEFAULT '0' COMMENT '1：已回收；0：可用；',
  `path` varchar(500) DEFAULT '',
  `count` int(11) DEFAULT '0' COMMENT '扫码次数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_shequshop_school_wechat_code`
--

LOCK TABLES `ims_shequshop_school_wechat_code` WRITE;
/*!40000 ALTER TABLE `ims_shequshop_school_wechat_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `ims_shequshop_school_wechat_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'yigoym'
--

--
-- Dumping routines for database 'yigoym'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-16 22:40:51
