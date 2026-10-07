<!--
 * @Descripttion: 开发规范
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: xiao li
 * @LastEditTime: 2021-03-16 09:48:41
-->
# 后台管理系统开发规范

build js文件中替换：
lbConfig.jsPath

## 命名规范
1、 JS命名规范: 请使用小驼峰命名法
> 如：let userName = 'xxx'

2、CSS命名规范：请使用小写 + 中划线拼接
> 如：.tab-bar

3、文件命名规范：请使用语义化命名，文件名必须和功能、项目的中文意思大致一致
> 如：商城  =》 shop、mall等

4、函数编写规范：尽量表明函数的用途，参数的说明
```code
   /**
     * @method 方法名
     * @param {String | 参数类型} 参数1说明
     * @param {Number | 参数类型} 参数2说明
     * @return {String | 返回类型}
     */
```

## 目录结构

```
|--build  vue自带的配置文件
|--config  本地环境与生产环境的配置
|  |--dev.env.js  本地环境配置文件
|  |--index.js  本地与开发的总配置文件
|  |--prod.env.js  开发环境配置文件
|--dist  打包出来的文件 （打包完后记得把static>js>manifest.js中的路径改了）
|--src  开发的总目录
|  |--api  接口api
|  |  |--modules  接口模块化(每个模块按模块名命名，每个接口请加上模块名的名字，以防全局污染)
|  |  |--index.js  接口的配置与接口统一处理异常错误
|  |--assets  静态文件、图片
|  |--components  通用组件和行业版的组件
|  |  |--basics  通用组件
|  |--ad.vue  页面右侧帮助中心的组件
|  |--footer.vue  页面底部组件 
|  |--header.vue  页面头部组件
|  |--layout.vue  页面内容区域组件（以及把其他公用组件组装）
|  |--sidebar.vue  页面菜单栏组件
|  |--i18n  多语言包
|  |  |--langs  多语言模块
|  |  |  |--en.json  英语
|  |  |  |--zh.json  中文
|  |  |--index.js  多语言配置
|  |--router 路由
|  |  |--_import_development.js  本地开发路由动态引入的方法
|  |  |--_import_production.js  生产环境路由动态引入的方法
|  |  |--index.js  白名单路由
|  |--store  数据状态管理
|  |  |--modules  数据状态模块
|  |  |--index.js  数据状态管理集中处理
|  |--style  全局的样式以及图标
|  |--utils  全局的公共方法和工具
|  |--view  所有的页面
|  |  |--404  找不到页面
|  |  |--custom  客户管理
|  |  |--examine  审核管理
|  |  |--finance  财务管理 
|  |  |--login  登录
|  |  |--shop  订单管理
|  |  |--system  系统设置
|  |--App.vue  vue最外层组件
|  |--Bus.js  eventBus数据传递
|  |--main.js  vue入口文件
|  |--permission.js  本地开发动态路由表
|--static  存放的Ueditor编辑器|
```

## 安装运行与打包
```找到文件【api/index.js】然后搜索【developmentURL】，将其更改为您自己的域名

``` bash
# 安装依赖包
npm install

# 本地运行
npm run dev

# 打包上线
npm run build
```