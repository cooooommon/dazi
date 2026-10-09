# 小程序虚拟支付接入部署说明（纯代币模式）

个人主体小程序虚拟支付已接入本项目，统一采用**代币充值模式（`short_series_coin`）**：
MP 后台已配置代币兑换比例（1 人民币 = 1 代币）。**充值、会员卡购买、服务/邀约订单、门店套餐**
全部直接拉起代币充值支付（signData 不含 productId/goodsPrice，**无需配置任何道具**）；
支付到账后按业务类型自动发货（充值入账户余额、会员卡开通会员、订单改为已支付）。

```
任意支付场景（充值/会员/订单/套餐）→ wx.requestVirtualPayment(mode=short_series_coin)
        → 代币入微信代币账户（平台自动入账）→ 推送/查单确认后按业务类型发货
        ├─ type=Balance     → 账户余额入账（含余额流水）
        ├─ type=memberOrder → 开通/续费会员
        ├─ type=Massage 等  → 订单状态改为已支付（走原有 orderResult 链路）
        └─ ...
```

「余额支付」选项仍保留（扣的是账户余额），与代币支付并存，用户可自选。

## 一、部署步骤

### 1. 建表 / 升级

- 首次：导入 `backend/virtualpay.sql`（2 张表：config / order；代币模式**不需要道具映射表**）。
- 已建过表：执行 SQL 里的 `ALTER TABLE ims_virtualpay_config ADD COLUMN coin_rate ...` 升级语句。
- 表前缀取自 `.env` 的 `database.prefix`（默认 `ims_`），改过就替换。

### 2. 配置 OfferID / AppKey / 兑换比例

```sql
INSERT INTO `ims_virtualpay_config`
(`uniacid`,`offer_id`,`app_key`,`env`,`enabled`,`coin_rate`,`create_time`,`update_time`)
VALUES (666, '你的OfferID', '你的现网AppKey', 0, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
```

- `coin_rate` 必须与 MP 后台【虚拟支付 → 代币配置】的兑换比例一致：1元=1代币 → 填 `1`。
- 若以后在后台改了兑换比例（如 1元=100代币），同步把 `coin_rate` 改成 `100`。
- 日常修改可在管理后台「系统设置 → 支付配置 → 虚拟支付配置」可视化操作（2026-10-09 新增）；
  app_key 留空表示不修改；关闭再开启不影响已存配置。首次初始化仍用上面的 SQL。

### 3. 配置发货推送 URL

MP 后台【虚拟支付 → 基本配置 → 基础配置 → 发货推送配置】填：

```
https://dazi.tunma.top/index.php/virtualpay/notify/receive
```

已实现的事件处理：`xpay_goods_deliver_notify`、`xpay_coin_pay_notify`（到账/发货）、`xpay_refund_notify`（退款）。
发货按业务类型分发（复用原微信支付回调同一批 orderResult 模型）：充值 → 余额入账；会员 → 开通会员；订单 → 已支付。

### 4. iOS（如需）

MP 后台【虚拟支付 → 基础配置】：配置**小程序简称**、开通**苹果 IAP 支付**。前端已做微信版本校验（iOS 需 ≥ 8.0.68）。

## 二、⚠️ 上线前必测：金额校验（1 元真单）

官方文档未逐字写明代币充值扣款公式，代码按「支付金额(分) = 代币数量 × 100 ÷ coin_rate」实现。
**第一次务必用小额真单验证**：

1. 后台临时加一个 **1 元**充值档位；
2. 小程序充值 1 元 → 微信收银台应显示 **¥1.00**（购买 1 代币）；
3. 支付成功后确认：账户余额 +1、`runtime/virtualpay.log` 有 `create` → `coin_notify`/`query_response` → `deliver_ok`。

若收银台显示的金额不是 ¥1.00，立即停止测试，按实际显示反推 `coin_rate`（显示 ¥0.01 → 填 100；显示 ¥100 → 需在后台把兑换比例调整为 1元=100代币 后填 100），改完再测。

**金额粒度**：兑换比例 1元=1代币 时，所有走代币支付的金额必须是**整数元**（会员卡 98/198 ✅、充值档位 50/100/300 ✅）。
小数金额（如 88.80 元）无法折算成整数代币，下单时会提示用户改用「余额支付」（先充值）；
若希望小数金额也能直付，把后台兑换比例改为 1元=100代币 并将 `coin_rate` 设为 `100`。

## 三、验证流程

1. 充值页选档位 → 代币支付 → 账户余额增加、余额流水生成。
2. 会员卡页选「微信支付」→ 代币支付 → 会员开通（不再需要先充值）。
3. 下服务订单（整数元）→ 微信支付 → 代币支付 → 订单已支付。
4. 「余额支付」路径回归一遍：余额足够扣款成功、余额不足有提示。
5. 兜底：`POST /index.php/virtualpay/index/checkPending`；前端支付成功后也会自动轮询订单状态。

## 四、排障

- 日志：`backend/runtime/virtualpay.log`（create 签名参数、notify 原文、query_response 平台返回）。
- 拉起支付报签名错误：核对 `app_key` 是否为**现网** AppKey、`session_key` 是否过期（重进小程序刷新）。
- 拉起支付报 `-15009 COIN_NOT_PUBLISH`：MP 后台【代币配置】未「发布」（改比例后需重新发布，变更可能需审核），发布完成后再试。
- 支付成功但未发货：看日志有无推送原文；查单兜底会自动补（代币充值属现金单，可用 query_order 查询）。
- 彻底关闭虚拟支付：`UPDATE ims_virtualpay_config SET enabled=0 WHERE uniacid=666;`（恢复原微信支付，无需改代码）。

## 五、已知边界

- 微信侧代币账户随每笔支付累积代币（平台自动入账），本项目不调用 `/xpay/currency_pay` 消耗代币；
  结算与账单按现金流水计算，不受影响。如后续需要真正的"代币消耗"闭环，可再接入 currency_pay。
- 退款：Android 在 MP 后台【交易订单】操作，iOS 由 Apple 处理；退款推送会把虚拟支付单标记「已退款」，
  **已发货权益/已入账余额的回退目前需人工处理**。
- 手续费：Android T+3 结算费率 1%；iOS 约 45-60 天、Apple 佣金 12%；个人主体月支付限额 10 万元。

## 六、参考

- 个人主体接入指引：https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/virtual-payment/person.html
- 虚拟支付（企业、个体户，含代币配置/currency_pay）：https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/virtual-payment.html
- wx.requestVirtualPayment（mode 枚举 short_series_goods / short_series_coin）：https://developers.weixin.qq.com/miniprogram/dev/api/payment/wx.requestVirtualPayment.html
- uni-app requestVirtualPayment 文档（代币充值 signData 示例）：https://uniapp.dcloud.net.cn/api/plugins/virtualPayment.html
