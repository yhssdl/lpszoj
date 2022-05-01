<?php


/* @var $this yii\web\View */
/* @var $settings array */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'Update');
?>
<p class="lead">感谢使用 LPSZOJ <?= file_get_contents(Yii::getAlias('@app/VERSION')) ?>。</p>

<a class="btn btn-default btn-block" href="https://gitee.com/yhssdl/lpszoj/blob/master/docs/update.md">了解如何更新</a><br>

<div class="animate__animated animate__fadeInUp">
  
    <p>
        如果你在使用过程中发现 Bug，或者希望增加一些额外的功能，欢迎使用
        <a href="https://gitee.com/yhssdl/lpszoj/issues" target="_blank">Issues</a> 来报告．
    </p>
    <p>项目主页：<a href="https://gitee.com/yhssdl/lpszoj/issues" target="_blank">LPSZOJ Gitee Project</a></p>
    <hr>
    <p>
        提示：当前页面打开可能会有些慢，是因为为了保证可以及时看到开发的变化，
        以下内容是读取自
        <a href="https://gitee.com/yhssdl/lpszoj/raw/master/CHANGELOG.md" target="_blank">https://gitee.com/yhssdl/lpszoj/raw/master/CHANGELOG.md</a>
        这个链接下的文件，在读取过程中访问慢导致的。
    </p>
    <hr>
    <div>
        <?= Yii::$app->formatter->asMarkdown($changelog, true) ?>
    </div>
</div>