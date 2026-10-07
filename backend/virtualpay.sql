-- ============================================================
-- 小程序虚拟支付（代币充值模式 short_series_coin）建表脚本
-- 说明：本项目数据库前缀取自 .env 的 database.prefix（默认 ims_）。
--       如你修改过前缀，请把下面所有表名的 ims_ 替换为实际前缀。
-- ============================================================

-- 1. 虚拟支付配置表（OfferID / 现网AppKey / 代币兑换比例）
CREATE TABLE IF NOT EXISTS `ims_virtualpay_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 666 COMMENT '站点id',
  `offer_id` varchar(64) NOT NULL DEFAULT '' COMMENT '虚拟支付 OfferID（MP后台-虚拟支付-基本配置）',
  `app_key` varchar(128) NOT NULL DEFAULT '' COMMENT '现网 AppKey（MP后台-虚拟支付-基本配置）',
  `env` tinyint(2) NOT NULL DEFAULT 0 COMMENT '环境标识，固定 0（现网）',
  `enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1启用虚拟支付 0停用（停用后走原微信支付）',
  `coin_rate` int(11) NOT NULL DEFAULT 1 COMMENT '后台代币兑换比例：1元=coin_rate个代币，须与 MP 后台【代币配置】一致（1元=1代币填1）',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_uniacid` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='虚拟支付配置';

-- 已建过表（无 coin_rate 字段）的执行这句升级：
-- ALTER TABLE `ims_virtualpay_config` ADD COLUMN `coin_rate` int(11) NOT NULL DEFAULT 1 COMMENT '后台代币兑换比例：1元=coin_rate个代币，须与 MP 后台代币配置一致';

-- 2. 道具映射表：代币模式下【不再需要】，无需创建（历史遗留可删除）

-- 3. 虚拟支付订单表（product_id/goods_price 为道具模式遗留字段，代币模式下恒为空/0，保留不影响）
CREATE TABLE IF NOT EXISTS `ims_virtualpay_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT 666,
  `out_trade_no` varchar(64) NOT NULL COMMENT '虚拟支付业务单号（下单生成，唯一）',
  `order_code` varchar(64) NOT NULL DEFAULT '' COMMENT '业务系统订单号',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '业务类型',
  `openid` varchar(64) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `product_id` varchar(64) NOT NULL DEFAULT '' COMMENT '道具ID（代币模式未使用）',
  `goods_price` int(11) NOT NULL DEFAULT 0 COMMENT '道具单价(分)（代币模式未使用）',
  `buy_quantity` int(11) NOT NULL DEFAULT 1 COMMENT '代币数量',
  `total_fee` int(11) NOT NULL DEFAULT 0 COMMENT '订单总额(分)',
  `wx_order_id` varchar(64) NOT NULL DEFAULT '' COMMENT '平台单号 wx_order_id',
  `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0待支付 1已支付已发货 2已退款',
  `pay_time` int(11) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_out_trade_no` (`out_trade_no`),
  KEY `idx_order_code` (`order_code`),
  KEY `idx_status` (`uniacid`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='虚拟支付订单';

-- ============================================================
-- 示例：配置（把 OfferID / AppKey 换成你在 MP 后台拿到的值；
--       coin_rate 与 MP 后台代币兑换比例保持一致，1元=1代币则为 1）
-- ============================================================
-- INSERT INTO `ims_virtualpay_config` (`uniacid`,`offer_id`,`app_key`,`env`,`enabled`,`coin_rate`,`create_time`,`update_time`)
-- VALUES (666, '你的OfferID', '你的现网AppKey', 0, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
