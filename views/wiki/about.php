<div class="alert alert-light">
    <i class="glyphicon glyphicon-info-sign"></i> 本 <b>OJ</b> 系统可对用户在线提交的源代码进行编译和执行，并通过预先设计的测试数据检验代码的正确性。
</div>
<p></p>
<div class="list-group">

    <div class="list-group-item">
        名称
        <span class="float-right text-secondary">
            <?= Yii::$app->setting->get('ojName') ?>
        </span>
    </div>
    <div class="list-group-item">
        OJ版本
        <span class="float-right text-secondary">
            <?= file_get_contents(Yii::getAlias('@app/VERSION')) ?>
        </span>
    </div>
</div>

<p></p>
<div class="list-group">
    <a class="list-group-item list-group-item-action" target="_blank" href="//gitee.com/yhssdl/lpszoj">
        项目源码
        <span class="float-right text-secondary">
            &gt;
        </span>
    </a>
    <a class="list-group-item list-group-item-action" target="_blank" href="//gitee.com/yhssdl/lpszoj/issues">
        问题反馈
        <span class="float-right text-secondary">
            &gt;
        </span>
    </a>
</div>