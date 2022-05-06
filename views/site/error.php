<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>


<div class="alert alert-danger">
    <i class="fa fa-info-circle"></i> <?= nl2br(Html::encode($message)) ?>
</div>

<div class="card animate__animated animate__fadeInUp">
    <div class="card-body">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
        页面没有如您所期望地加载，请确认您输入的地址无误且拥有访问当前页面的权限。<br>
        如果您认为这是站点本身的问题，欢迎您联系 <a target="_blank" href="https://gitee.com/yhssdl/lpszoj">Online Judge 开发组</a>。

    </div>
</div>
<br>
<div class="btn-group btn-group-justified">
    <div class="btn-group">
        <a class="btn btn-default" href="javascript:history.go(-1)"> <i class="fas fa-fw fa-arrow-alt-circle-left"></i> 返回上一页</a>
    </div>
    <div class="btn-group">
        <a class="btn btn-default" href="<?= Yii::$app->homeUrl ?>"> <i class="fas fa-fw fa-home"></i> 返回首页</a>
    </div>
</div>