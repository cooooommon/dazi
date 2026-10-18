-- =====================================================================
-- 生产库（dazi.tunma.top）上线前测试/演示数据清理脚本
-- 整理日期：2026-10-14（依据已核对的行数：订单1385/用户174/向导240/渠道91/经纪人103/分销商63等）
--
-- ⚠️ 执行铁律：
--   1. 先全库备份：mysqldump -u用户 -p 数据库名 > backup_before_launch.sql
--   2. 低峰期执行；TRUNCATE 不可回滚
--   3. 每张表执行前 SELECT COUNT(*) 核对行数与预期一致
--   4. 「待确认组」默认注释状态，核对确认是演示数据后再逐条放开
-- =====================================================================

-- =====================================================================
-- 批次 1：订单及衍生（含加钟/退款/需求单/分佣/会员订单/门店套餐订单）
-- =====================================================================
TRUNCATE TABLE ims_massage_service_order_list;                 -- 服务订单主表（1385条）
TRUNCATE TABLE ims_massage_service_order_list_data;            -- 订单统计数据
TRUNCATE TABLE ims_massage_service_order_goods_list;           -- 订单服务明细
TRUNCATE TABLE ims_massage_service_order_address;              -- 订单地址
TRUNCATE TABLE ims_massage_service_order_price_log;            -- 改价日志
TRUNCATE TABLE ims_massage_service_order_comment;              -- 订单评价（85条）
TRUNCATE TABLE ims_massage_service_order_comment_goods;        -- 评价内容明细
TRUNCATE TABLE ims_massage_service_order_commission;           -- 分佣记录（3042条）
TRUNCATE TABLE ims_massage_service_order_commission_goods;     -- 商品订单分佣
TRUNCATE TABLE ims_massage_service_order_commission_share;     -- 分享分佣
TRUNCATE TABLE ims_massage_service_refund_order;               -- 退款单（490条）
TRUNCATE TABLE ims_massage_service_refund_order_goods;         -- 退款明细
TRUNCATE TABLE ims_massage_service_up_order_list;              -- 加钟订单（86条）
TRUNCATE TABLE ims_massage_service_up_order_goods_list;        -- 加钟明细
TRUNCATE TABLE ims_massage_service_demand_order;               -- 需求订单（195条）
TRUNCATE TABLE ims_massage_service_demand_order_apply;         -- 需求单报名
TRUNCATE TABLE ims_massage_service_many_demand_order;          -- 多向导需求单
TRUNCATE TABLE ims_massage_order_coach_change_logs;            -- 订单改派日志
TRUNCATE TABLE ims_massage_order_control_log;                  -- 订单操作日志
TRUNCATE TABLE ims_massage_member_order_list;                  -- 会员订单（86条，含0元测试单）
TRUNCATE TABLE ims_massage_member_order_integral;              -- 会员订单积分
TRUNCATE TABLE ims_massage_store_package_order_list;           -- 门店套餐订单
TRUNCATE TABLE ims_massage_store_package_order_goods_list;     -- 套餐订单明细
TRUNCATE TABLE ims_massage_store_package_order_hx_list;        -- 套餐核销记录
TRUNCATE TABLE ims_massage_store_package_order_comment_list;   -- 套餐订单评价
TRUNCATE TABLE ims_massage_store_package_order_refund_list;    -- 套餐退款单
TRUNCATE TABLE ims_massage_store_package_order_refund_goods_list; -- 套餐退款明细

-- =====================================================================
-- 批次 2：资金流水（清完各用户/向导余额字段随表归零）
-- =====================================================================
TRUNCATE TABLE ims_massage_service_wallet_list;                -- 钱包流水（172条，7类角色）
TRUNCATE TABLE ims_massage_service_cash_list;                  -- 提现记录（0条，保持0）
TRUNCATE TABLE ims_massage_channel_cash_water;                 -- 渠道商流水（3条）
TRUNCATE TABLE ims_massage_integral_list;                      -- 积分流水（31条）
TRUNCATE TABLE ims_massage_member_user_integral;               -- 会员积分

-- =====================================================================
-- 批次 3：虚拟支付（只清订单，配置绝对不动）
-- =====================================================================
TRUNCATE TABLE ims_virtualpay_order;                           -- 虚拟支付订单（1条1元测试单）
-- ims_virtualpay_config 是虚拟支付配置，不删！

-- =====================================================================
-- 批次 4：用户及衍生（全删；若有保留账号先发 id 改按 id 删）
-- =====================================================================
TRUNCATE TABLE ims_massage_service_user_list;                  -- 用户（174条测试/演示）
TRUNCATE TABLE ims_massage_service_user_label_data;            -- 用户打标记录
TRUNCATE TABLE ims_massage_user_channel;                       -- 用户渠道关系
TRUNCATE TABLE ims_massage_user_from_list;                     -- 用户来源
TRUNCATE TABLE ims_massage_user_store_package_collect;         -- 套餐收藏

-- =====================================================================
-- 批次 5：向导及衍生（全删；等级配置不动）
-- =====================================================================
TRUNCATE TABLE ims_massage_service_coach_list;                 -- 向导（240条演示）
TRUNCATE TABLE ims_massage_service_service_coach;              -- 服务×向导绑定（服务提成层）
TRUNCATE TABLE ims_massage_service_service_coach_bak;          -- 绑定备份表
TRUNCATE TABLE ims_massage_service_coach_time;                 -- 向导时间
TRUNCATE TABLE ims_massage_service_coach_time_list;            -- 向导时间明细
TRUNCATE TABLE ims_massage_coach_work_log;                     -- 上班日志
TRUNCATE TABLE ims_massage_coach_time_log;                     -- 时间日志
TRUNCATE TABLE ims_massage_service_coach_appeal;               -- 申诉
TRUNCATE TABLE ims_massage_service_coach_police;               -- 举报
TRUNCATE TABLE ims_massage_service_coach_update;               -- 向导资料变更申请
TRUNCATE TABLE ims_massage_service_coach_collect;              -- 向导收藏
TRUNCATE TABLE ims_massage_service_coach_feedback;             -- 向导反馈
TRUNCATE TABLE ims_massage_coach_entry_agreement;              -- 入驻协议签署
-- ims_massage_service_coach_level 向导等级配置，不删！

-- =====================================================================
-- 批次 6：渠道商 / 经纪人 / 分销商（全删；分类与分销规则配置不动）
-- =====================================================================
TRUNCATE TABLE ims_massage_channel_list;                       -- 渠道商（91条演示）
TRUNCATE TABLE ims_massage_channel_staff_list;                 -- 渠道员工
TRUNCATE TABLE ims_massage_broker_list;                        -- 经纪人（103条演示）
TRUNCATE TABLE ims_massage_distribution_list;                  -- 分销商（63条演示）
-- ims_massage_channel_cate 渠道分类、ims_massage_distribution_config 分销规则，不删！

-- =====================================================================
-- 批次 7：其他测试痕迹（动态/券领取/日志/申请/报名）
-- =====================================================================
TRUNCATE TABLE ims_massage_dynamic_list;                       -- 动态（29条）
TRUNCATE TABLE ims_massage_dynamic_comment;                    -- 动态评论
TRUNCATE TABLE ims_massage_dynamic_thumbs;                     -- 动态点赞
TRUNCATE TABLE ims_massage_dynamic_follow;                     -- 动态关注
TRUNCATE TABLE ims_massage_dynamic_watch_record;               -- 动态浏览记录
TRUNCATE TABLE ims_massage_service_coupon_record;              -- 领券记录（46条）
TRUNCATE TABLE ims_massage_service_coupon_atv_record;          -- 券活动参与记录
TRUNCATE TABLE ims_massage_service_coupon_atv_record_list;     -- 活动记录明细
TRUNCATE TABLE ims_massage_service_coupon_atv_record_coupon;   -- 活动领取的券
TRUNCATE TABLE ims_massage_action_log;                         -- 后台操作日志
TRUNCATE TABLE ims_massage_aliyun_phone_record;                -- 通话记录
TRUNCATE TABLE ims_massage_aliyun_play_record;                 -- 通话录音
TRUNCATE TABLE ims_massage_aliyun_reminder_record;             -- 提醒记录
TRUNCATE TABLE ims_massage_article_sub_data;                   -- 文章报名数据
TRUNCATE TABLE ims_massage_article_sub_list;                   -- 文章报名
TRUNCATE TABLE ims_massage_store_apply;                        -- 门店入驻申请
TRUNCATE TABLE ims_massage_store_apply_update;                 -- 门店申请变更

-- =====================================================================
-- 批次 8：后台账号与角色（只保留 admin 超管）
-- 依据：is_admin=1 为超管不依赖角色；节点过滤只对 is_admin=2 生效
-- =====================================================================
DELETE FROM ims_massage_role_admin WHERE uniacid = 666;        -- 账号-角色绑定（全清）
DELETE FROM ims_massage_role_node WHERE uniacid = 666;         -- 角色权限节点
DELETE FROM ims_massage_role_list WHERE uniacid = 666;         -- 角色
DELETE FROM ims_shequshop_school_admin
WHERE uniacid = 666 AND username <> 'admin';                   -- 账号（含演示代理商，只留admin）

-- =====================================================================
-- 批次 9：演示基础数据（与本地清理同范围，生产核对后执行）
-- =====================================================================
TRUNCATE TABLE ims_massage_service_service_list;               -- 服务（生产未统计，先COUNT）
TRUNCATE TABLE ims_massage_service_service_type_list;          -- 服务分类
TRUNCATE TABLE ims_massage_store_package_list;                 -- 门店套餐
TRUNCATE TABLE ims_massage_store_package_sku;                  -- 套餐SKU
TRUNCATE TABLE ims_massage_store_package_sku_price;            -- 套餐SKU价格
TRUNCATE TABLE ims_massage_store_package_seckill_list;         -- 套餐秒杀
TRUNCATE TABLE ims_massage_service_car;                        -- 车费配置
TRUNCATE TABLE ims_massage_service_car_price;                  -- 车费价格
TRUNCATE TABLE ims_massage_service_balance_order_list;         -- 余额订单
TRUNCATE TABLE ims_massage_service_balance_refund_order;       -- 余额退款单
TRUNCATE TABLE ims_massage_service_balance_water;              -- 余额流水
TRUNCATE TABLE ims_massage_service_coupon;                     -- 券模板
TRUNCATE TABLE ims_massage_service_coupon_goods;               -- 券-服务关联
TRUNCATE TABLE ims_massage_service_coupon_atv;                 -- 券活动
TRUNCATE TABLE ims_massage_service_coupon_atv_coupon;          -- 活动-券关联
TRUNCATE TABLE ims_massage_store_type_list;                    -- 门店分类
TRUNCATE TABLE ims_massage_service_city_list;                  -- 开通城市
TRUNCATE TABLE ims_massage_service_address;                    -- 地址库
TRUNCATE TABLE ims_massage_service_banner;                     -- banner
TRUNCATE TABLE ims_massage_service_demand_type;                -- 需求类型
TRUNCATE TABLE ims_massage_service_notice_list;                -- 公告
TRUNCATE TABLE ims_shequshop_school_wechat_code;               -- 小程序码记录
TRUNCATE TABLE ims_massage_join_form;                          -- 代理/入驻申请
TRUNCATE TABLE ims_massage_article_list;                       -- 文章
TRUNCATE TABLE ims_massage_article_connect;                    -- 文章关联
TRUNCATE TABLE ims_massage_article_form_field;                 -- 文章表单字段
TRUNCATE TABLE ims_massage_service_shop_goods;                 -- 商品
TRUNCATE TABLE ims_massage_service_shop_carte;                 -- 商品分类
TRUNCATE TABLE ims_massage_service_comment_lable;              -- 评价标签
TRUNCATE TABLE ims_massage_service_user_label_list;            -- 客户标签本体
TRUNCATE TABLE ims_massage_member_card_list;                   -- 会员卡
TRUNCATE TABLE ims_massage_service_balance_card;               -- 余额卡
TRUNCATE TABLE ims_massage_service_coach_tag;                  -- 向导标签
TRUNCATE TABLE ims_massage_service_label;                      -- 服务标签
TRUNCATE TABLE ims_massage_store_list;                         -- 演示门店
TRUNCATE TABLE ims_shequshop_school_attachment;                -- 图片元数据（磁盘文件不删）
TRUNCATE TABLE ims_massage_channel_cate;                	   -- 渠道商分类

-- =====================================================================
-- 执行后验证
-- =====================================================================
-- 1) 流水应全部为 0：
-- SELECT '订单' t,COUNT(*) c FROM ims_massage_service_order_list
-- UNION ALL SELECT '用户',COUNT(*) FROM ims_massage_service_user_list
-- UNION ALL SELECT '向导',COUNT(*) FROM ims_massage_service_coach_list
-- UNION ALL SELECT '钱包流水',COUNT(*) FROM ims_massage_service_wallet_list
-- UNION ALL SELECT '虚拟支付单',COUNT(*) FROM ims_virtualpay_order;
-- 2) 配置应完好：cash_type=1（浮动）、虚拟支付配置在、等级在、admin 在：
-- SELECT cash_type FROM ims_shequshop_school_config WHERE uniacid=666;
-- SELECT COUNT(*) FROM ims_virtualpay_config;
-- SELECT COUNT(*) FROM ims_massage_service_coach_level;
-- SELECT username,is_admin FROM ims_shequshop_school_admin WHERE uniacid=666;  -- 只剩 admin/1
--
-- 收尾：清 backend/runtime/cache/ → admin 重新登录后台逐页检查 →
--       小程序重新登录走一遍浏览/下单（创建后取消，不付款）
-- =====================================================================
-- 绝不触碰：ims_shequshop_school_config / ims_massage_config / _config_setting /
--   ims_virtualpay_config / ims_shequshop_school_pay_config / ims_massage_service_coach_level /
--   ims_massage_channel_cate / ims_massage_distribution_config / ims_shequshop_school_admin /
--   ims_massage_role_*（角色按批次8的 DELETE 清，不 TRUNCATE）/ ims_massage_action_diy
-- =====================================================================
