# 虚拟支付配置接入后台「系统设置 → 支付配置」设计

日期：2026-10-09 ｜ 状态：已确认（方案A，用户拍板）

## 目标

在小程序管理后台「系统设置 → 支付配置」页新增第三个标签页「虚拟支付配置」，
可视化维护 `ims_virtualpay_config`（offer_id / app_key / enabled / coin_rate），
与现有「微信支付配置」「支付宝支付配置」两个标签页完全同构。

## 方案对比

- **A（采纳）**：页内第三个 tab，复刻 payment/wechat + payment/alipay 的 pagePermission 模式，后端零新增（复用已有 `virtualpay/admin/AdminSetting/configInfo|configUpdate`）。
- B（弃）：在 payment.vue 内堆表单 —— 破坏 tab 结构。
- C（弃）：侧边栏独立一级菜单 —— 与其他支付配置割裂。

## 改动清单

| 文件 | 改动 |
|---|---|
| `web/src/view/system/payment/virtualpay.vue` | 新建：启用开关（enabled 单选）、OfferID、现网AppKey（留空=不修改，展示已配置脱敏值）、代币兑换比例 coin_rate（正整数）；顶部提示条说明 MP后台取值位置、coin_rate 一致性、发货推送 URL；env 不露出（固定 0） |
| `web/src/api/modules/system.js` | +`virtualpayConfigInfo` / `virtualpayConfigUpdate` → POST `virtualpay/admin/AdminSetting/configInfo|configUpdate` |
| `web/src/permission.js` | payment/alipay 路由的 pagePermission 各加第三项 `SystemPaymentVirtualpay(url:'virtualpay', index:2)`；新增 virtualpay 路由 → `/system/payment/virtualpay` |
| `web/src/i18n/langs/zh.json` `en.json` | menu 下加 `SystemPaymentVirtualpay`（虚拟支付配置 / Virtual Payment） |
| `backend/app/virtualpay/route/route.php` | `configInfo` 由 GET 改 POST（前端统一 post()，与 massage 管理端约定一致） |
| `backend/app/virtualpay/controller/AdminSetting.php` | 允许 enabled=0 且从未配置时保存（不再强制要求 app_key，关闭态无需密钥） |
| `backend/virtualpay-deploy.md` | 补一句：配置可在后台「系统设置→支付配置→虚拟支付配置」可视化修改 |

## 数据流

页面加载 `configInfo` → 渲染表单（app_key 为脱敏值，仅提示已配置）；提交 `configUpdate`
→ 后端 upsert `ims_virtualpay_config`（app_key 留空不覆盖；env 缺省 0）→ 影响下次 `VirtualPayService::tryCreate/enabled()` 读取。

## 边界与错误处理

- app_key 校验：仅当 enabled=1 且库里无 key 时必填（后端兜底同逻辑）。
- coin_rate：el-input-number 正整数 ≥1；改动须与 MP后台【代币配置】同步（提示条说明）。
- 保存成功后重新拉取 configInfo 刷新脱敏 key。

## 验证

- 后端：`php -l`；本地起服务 POST configInfo/configUpdate（无 token 期望鉴权错误而非 404/500，证明路由通）。
- 前端：ESLint 通过；dev server 编译通过（页面级渲染验证需登录态，交付后由用户在后台确认 tab 出现）。
