<p align="center">
    <a href="https://www.jnoj.org" target="_blank">
        <img src="docs/favicon.ico" height="100px">
    </a>
    <h1 align="center">Lpsz Online Judge</h1>
    <br>
</p>

演示网址： [LPSZOJ](http://oj.msns.cn:2080/)

LPSZOJ 派生自 [JNOJ](https://github.com/shi-yang/jnoj)，参考了 [SCNUOJ](https://github.com/scnu-socoding/scnuoj)界面设计，是一个在线评测系统。

LPSZOJ 可对用户在线提交的源代码进行编译和执行，并通过预先设计的测试数据检验代码的正确性。

帮助文档
--------

1. [安装教程](docs/install.md)
2. [更新教程](docs/update.md)
3. 如有任何问题，可新建 Issue 或联系 [yhssdl](https://gitee.com/yhssdl/lpszoj)

### 和 JNOJ 相比有何亮点？

- 重新设计的界面。
- 重新设置比赛排名规则，OI 赛制按最后一次提交总分排名，IOI 赛制按最高分提交总分排名。
- 支持永久题目集（榜单类似 Codeforces 的补题榜）、限时题目集和站外比赛。
- 新增训练模块，可以让练习方式变得更加丰富。
- 新增数据库备份与恢复，以及 SQL 命令自定义与运行功能。
- 整合线上线下赛各项功能，额外提供打星用户自定义、比赛邀请码等可定制项，解锁更多可能。

目录结构
----------

      assets/             资源文件的定义
      commands/           控制台命令
      components/         Web 应用程序组件
      config/             Web 应用程序配置信息
      controllers/        控制器(Controller)文件
      docs/               文档目录
      judge/              判题机所在目录
      judge/data          判题数据目录
      mail/               发邮件时的视图模板
      messages/           多语言翻译
      migrations/         数据库迁移时的各种代码
      models/             模型(Model)文件
      modules/admin       Web 后台应用
      modules/polygon     多边形出题系统
      runtime/            Web 程序运行时生成的缓存
      tests/              各种测试
      vendor/             第三方依赖
      views/              视图(View)文件
      web/                Web 入口目录
      widgets/            各种插件
      socket.php          用于启动 Socket，提供消息通知功能
