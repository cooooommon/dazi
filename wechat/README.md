## longbingx 
为提高团队协作效率，便于前端后期优化维护，请大家认真阅读以下内容：


## 开发规范
1、命名规范
```
（1）目录使用小写+下划线，例如：longbing_card 
（2）组件使用小写+连字符，例如：tab-bar
（3）函数及参数使用驼峰式命名，例如：userInfo
（4）分包必须是跟需求的内容相关的词，例如：boss
```

2、注释规范（简洁明了） 
```
（1）公共组件使用说明
（2）各组件中重要函数或者类说明
（3）方法需写明作者、用途及复杂的业务逻辑处理说明
（4）单行注释使用//  
（5）注释块必须以/（至少两个星号）开头/
```

3、编码规范 
```
（1）v-for 循环必须加上 key 属性，在整个 for 循环中 key 需要唯一
（2）避免 v-if 和 v-for 同时用在一个元素上
```

4、CSS 规范 
```
（1）名称统一使用连字符，例如：goods-item
（2）省略值为0时的单位，例如：padding：0
```


## 目录结构 
```
｜--technician				向导端
｜	｜--pages					分包页面
｜	｜	｜--apply				申请/编辑向导信息
｜--apis						接口，统一管理
｜	|--technician.js			向导端接口，命名与对应对分包名一致
｜	|--base.js					常用接口，例如：登陆/更新用户信息/系统配置等～
｜	|--index.js					引入接口，命名+Model，例如：import baseModel from './base.js';
｜--components				公共组件，
｜	｜--abnor					页面无数据处理样式
｜	｜--auth						通用授权，包括：授权用户信息以及授权手机号码
｜	｜--banner					轮播图
｜	｜--column					分类列表
｜	｜--jyf-Parser				富文本解析
｜	｜--load-more				加载更多数据
｜	｜--search					搜索
｜	｜--share					点击分享按钮，弹出分享面版
｜	｜--upload					图片上传
｜	｜--tab						选项卡切换
｜--pages					主包
｜--static					图片资源，文件夹命名以功能模块划分
｜--store					全局数据
｜	｜--modules					
｜	｜	｜--config.js				全局配置信息，configInfo
｜	｜	｜--user.js					用户信息，包括：userInfo，commonOptions：公共参数
｜--styles					CSS样式，文件以类别或功能模块划分
｜	｜--icon.wxss				图标库，更新后请在iconfont样式里面添加一句：line-height:1;font-size:32rpx;
｜	｜--index.wxss				引入其他样式，例如：@import './icon.wxss'
｜--utils
｜	｜--error.js				通用报错信息
｜	｜--index.js				公共方法，例如：goUrl跳转方法/getBmapLocation百度定位等～
｜	｜--validate.js			验证规则
｜--siteinfo.js				配置文件
```

## 页面兼容
```
页面字体颜色及按钮颜色需使用configInfo里面的主色primaryColor/辅色subColor 
```


##【腾讯文档】如何打包预约按摩H5？
https://docs.qq.com/doc/DWmhnZVN3eExLeHFZ

##【腾讯文档】如何打包预约按摩APP？
https://docs.qq.com/doc/DWnVheWFDZnlSTndX