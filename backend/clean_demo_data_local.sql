-- =====================================================================
-- 本地库（dazi @ 127.0.0.1:3323）演示数据清理脚本
-- 目的：清除源码厂商预置的演示数据，得到「新装干净」状态
-- 注意：TRUNCATE 不可回滚且自增 ID 归零；执行前先备份
--       mysqldump -uroot -p -P3323 dazi > backup_local_before_clean.sql
-- 整理日期：2026-10-14
-- 说明：用户/向导/订单/资金等业务流水表本地本就是空表，未列在本脚本
-- =====================================================================

-- ---------------------------------------------------------------------
-- 一、服务与门店套餐
-- ---------------------------------------------------------------------
TRUNCATE TABLE ims_massage_service_service_list;              -- 服务（144条演示）
TRUNCATE TABLE ims_massage_service_service_type_list;         -- 服务分类（31条演示）
TRUNCATE TABLE ims_massage_store_package_list;                -- 门店套餐（153条演示）
TRUNCATE TABLE ims_massage_store_package_sku;                 -- 套餐SKU（206条演示）
TRUNCATE TABLE ims_massage_store_package_sku_price;           -- 套餐SKU价格（281条演示）
TRUNCATE TABLE ims_massage_store_package_seckill_list;        -- 套餐秒杀（299条演示）
TRUNCATE TABLE ims_massage_service_car;                       -- 车费配置（172条演示）
TRUNCATE TABLE ims_massage_service_car_price;                 -- 车费价格（6条演示）

-- ---------------------------------------------------------------------
-- 二、资金流水与余额类
-- ---------------------------------------------------------------------
TRUNCATE TABLE ims_massage_service_balance_order_list;        -- 余额订单（134条演示）
TRUNCATE TABLE ims_massage_service_balance_refund_order;      -- 余额退款单（空表，同类别）
TRUNCATE TABLE ims_massage_service_balance_water;             -- 余额流水（3265条演示）

-- ---------------------------------------------------------------------
-- 三、优惠券（后台「卡券管理」）
-- ---------------------------------------------------------------------
TRUNCATE TABLE ims_massage_service_coupon;                    -- 券模板（12条演示）
TRUNCATE TABLE ims_massage_service_coupon_goods;              -- 券-服务关联（259条演示）
TRUNCATE TABLE ims_massage_service_coupon_atv;                -- 券活动（1条演示）
TRUNCATE TABLE ims_massage_service_coupon_atv_coupon;         -- 活动-券关联（1条演示）
TRUNCATE TABLE ims_massage_service_coupon_record;             -- 领券记录（空表）
TRUNCATE TABLE ims_massage_service_coupon_atv_record;         -- 活动参与记录（空表）
TRUNCATE TABLE ims_massage_service_coupon_atv_record_list;    -- 活动记录明细（空表）
TRUNCATE TABLE ims_massage_service_coupon_atv_record_coupon;  -- 活动领取的券（空表）

-- ---------------------------------------------------------------------
-- 四、门店基础数据
-- ---------------------------------------------------------------------
TRUNCATE TABLE ims_massage_store_type_list;                   -- 门店分类（133条演示）
TRUNCATE TABLE ims_massage_service_city_list;                 -- 开通城市（60条演示）
TRUNCATE TABLE ims_massage_service_address;                   -- 地址库（59条演示）
TRUNCATE TABLE ims_massage_service_banner;                    -- banner（28条演示）
TRUNCATE TABLE ims_massage_service_demand_type;               -- 需求类型（23条演示）
TRUNCATE TABLE ims_massage_service_notice_list;               -- 公告（2247条演示）

-- ---------------------------------------------------------------------
-- 五、代理/入驻申请（后台「代理申请」就是 join_form 表）
-- ---------------------------------------------------------------------
TRUNCATE TABLE ims_massage_join_form;                         -- 代理/入驻申请（31条演示）

-- ---------------------------------------------------------------------
-- 六、文章 / 商品 / 动态 / 评论
-- ---------------------------------------------------------------------
TRUNCATE TABLE ims_massage_article_list;                      -- 文章（4条演示）
TRUNCATE TABLE ims_massage_article_connect;                   -- 文章关联（4条演示）
TRUNCATE TABLE ims_massage_article_form_field;                -- 文章表单字段（3条演示）
TRUNCATE TABLE ims_massage_article_sub_list;                  -- 文章报名（空表）
TRUNCATE TABLE ims_massage_article_sub_data;                  -- 文章报名数据（空表）
TRUNCATE TABLE ims_massage_service_shop_goods;                -- 商品（3条演示）
TRUNCATE TABLE ims_massage_service_shop_carte;                -- 商品分类（3条演示）
TRUNCATE TABLE ims_massage_dynamic_list;                      -- 动态（空表）
TRUNCATE TABLE ims_massage_dynamic_comment;                   -- 动态评论（空表）
TRUNCATE TABLE ims_massage_dynamic_thumbs;                    -- 动态点赞（空表）
TRUNCATE TABLE ims_massage_dynamic_follow;                    -- 动态关注（空表）
TRUNCATE TABLE ims_massage_dynamic_watch_record;              -- 动态浏览记录（空表）
TRUNCATE TABLE ims_massage_service_order_comment;             -- 订单评价（空表）
TRUNCATE TABLE ims_massage_service_order_comment_goods;       -- 评价内容明细（空表）
TRUNCATE TABLE ims_massage_service_comment_lable;             -- 评价标签（14条演示）

-- ---------------------------------------------------------------------
-- 七、客户标签
-- ---------------------------------------------------------------------
TRUNCATE TABLE ims_massage_service_user_label_list;           -- 客户标签本体（8条演示）
TRUNCATE TABLE ims_massage_service_user_label_data;           -- 客户打标记录（空表）

-- ---------------------------------------------------------------------
-- 八、其余演示数据（最后一轮补充清理）
-- ---------------------------------------------------------------------
TRUNCATE TABLE ims_massage_member_card_list;                  -- 会员卡（29条演示）
TRUNCATE TABLE ims_massage_service_balance_card;              -- 余额卡（17条演示）
TRUNCATE TABLE ims_massage_service_coach_tag;                 -- 向导标签（13条演示）
TRUNCATE TABLE ims_massage_store_list;                        -- 演示门店（1条）
TRUNCATE TABLE ims_shequshop_school_attachment;               -- 演示图片元数据（2776条；磁盘文件不删）
TRUNCATE TABLE ims_shequshop_school_wechat_code;              -- 小程序码记录（86条演示）
-- TRUNCATE TABLE ims_massage_service_lable;                     -- 服务标签（4条演示）

-- =====================================================================
-- 执行后验证：以下查询应全部返回 0
-- =====================================================================
-- SELECT '服务' t,COUNT(*) c FROM ims_massage_service_service_list
-- UNION ALL SELECT '套餐',COUNT(*) FROM ims_massage_store_package_list
-- UNION ALL SELECT '余额流水',COUNT(*) FROM ims_massage_service_balance_water
-- UNION ALL SELECT '公告',COUNT(*) FROM ims_massage_service_notice_list
-- UNION ALL SELECT '附件',COUNT(*) FROM ims_shequshop_school_attachment
-- UNION ALL SELECT '会员卡',COUNT(*) FROM ims_massage_member_card_list
-- UNION ALL SELECT '客户标签',COUNT(*) FROM ims_massage_service_user_label_list;

-- 清理后建议：删除 backend/runtime/cache/ 目录内容（目录保留），清除页面缓存

-- =====================================================================
-- 保留清单（本脚本绝不触碰）：
--   ims_shequshop_school_config / ims_massage_config / ims_massage_config_setting
--   ims_virtualpay_config / ims_shequshop_school_pay_config / ims_shequshop_school_oos_config
--   ims_massage_service_coach_level（向导等级）/ ims_massage_channel_cate（渠道分类）
--   ims_shequshop_school_admin（admin 账号）/ ims_massage_action_diy（首页装修）
--   短信/语音/打印机/屏蔽词/加钟/帮助公告等 1 行配置表
-- =====================================================================
