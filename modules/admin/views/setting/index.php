<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $settings array */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Setting');

$editorName = Yii::$app->setting->get('ojEditor');
if($editorName=='app\\widgets\\kindeditor\\KindEditor'){
    $editorName = 'app\\widgets\\ckeditor\\CKeditor';
}


?>
<p class="lead">设置系统选项</p>
<div class="setting-form animate__animated animate__fadeInUp">

    <?= Html::beginForm() ?>

    <div class="form-group">
        <?= Html::radioList('isNotice', $settings['isNotice'], [
            1 => '开启全局公告',
            0 => '关闭全局公告'
        ]) ?>
        <p class="hint-block">
            开启后，全局公告将展示在每个页面的上方。
        </p>
    </div>

    <div class="form-group">
        <?php
        echo $editorName::widget(['name' => 'notice', 'id' => 'notice', 'value' => $settings['notice']]);
        ?>
    </div>


    <div class="form-group">
        <?= Html::radioList('isHomeNotice', $settings['isHomeNotice'], [
            1 => '开启首页公告',
            0 => '关闭首页公告'
        ]) ?>
        <p class="hint-block">
            开启后，首页公告将展示在首页的右上方。
        </p>
    </div>
    <div class="form-group">
        <?php
        echo $editorName::widget(['name' => 'homeNotice', 'id' => 'homeNotice', 'value' => $settings['homeNotice']]);
        ?>
    </div>
</div>

<div class="form-group">
    <div class="input-group"><span class="input-group-addon">系统名称</span>
        <?= Html::textInput('ojName', $settings['ojName'], ['class' => 'form-control']) ?>
    </div>
</div>

<div class="form-group">
    <div class="input-group"><span class="input-group-addon">学校名称</span>
        <?= Html::textInput('schoolName', $settings['schoolName'], ['class' => 'form-control']) ?>
    </div>
</div>

<div class="form-group">
    <div class="input-group"><span class="input-group-addon">提交间隔</span>
        <?= Html::textInput('submitTime', $settings['submitTime'], ['class' => 'form-control']) ?>
    </div>
</div>
<p class="hint-block">
    时间单位为秒，设为 0 时不限制提交，否则提交后必须间隔指定时间后才能再次提交，可避免用户短时间重复提交的情况。
</p>

<div class="form-group">
    <div class="input-group"><span class="input-group-addon">解榜时间</span>
        <?= Html::textInput('scoreboardFrozenTime', $settings['scoreboardFrozenTime'], ['class' => 'form-control']) ?>
    </div>

    <p class="hint-block">单位：秒。这个时间是从比赛结束后开始计算，如值为
        <?= $settings['scoreboardFrozenTime'] ?> 时，表示比赛结束 <?= intval($settings['scoreboardFrozenTime'] / 3600) ?> 个小时后不再封榜。
    </p>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'OI 模式'), 'oiMode') ?>
    <?= Html::radioList('oiMode', $settings['oiMode'], [
        1 => '是',
        0 => '否'
    ]) ?>
    <p class="hint-block">
        注意，如需启动 OI 模式，除了在此处选择是外，还需要在启动判题服务时加上 sudo ./dispatcher <code>-o</code>参数。
    </p>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'Show Training'), 'isShowTraining') ?>
    <?= Html::radioList('isShowTraining', $settings['isShowTraining'], [
        1 => '导航栏中显示训练菜单',
        0 => '导航栏中关闭训练菜单'
    ]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', 'Show Status'), 'isShowStatus') ?>
    <?= Html::radioList('isShowStatus', $settings['isShowStatus'], [
        1 => '导航栏中显示状态菜单',
        0 => '导航栏中关闭状态菜单'
    ]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '启用Polygon出题系统'), 'isEnablePolygon') ?>
    <?= Html::radioList('isEnablePolygon', $settings['isEnablePolygon'], [
        1 => '启用Polygon出现系统',
        0 => '关闭Polygon出题系统'
    ]) ?>
        <p class="hint-block">
        开启该功能后,问题列表界面会显示Polygon出现按钮,普通用户可以自主出题，管理员可以后台导入Polygon题目。
    </p>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '用户注册'), 'isUserReg') ?>
    <?= Html::radioList('isUserReg', $settings['isUserReg'], [
        1 => '开放',
        0 => '关闭'
    ]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '开启讨论'), 'isDiscuss') ?>
    <?= Html::radioList('isDiscuss', $settings['isDiscuss'], [
        1 => '开启',
        0 => '关闭'
    ]) ?>
</div>


<div class="form-group">
    <?= Html::label(Yii::t('app', '是否要共享代码'), 'isShareCode') ?>
    <?= Html::radioList('isShareCode', $settings['isShareCode'], [
        1 => '用户可以查看其他用户的代码',
        0 => '用户的代码只能由自己或者管理员查看',
        3 => '小组组长与管理员可以查看代码',
        2 => '只有管理员可以查看代码'
    ]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '是否可查看错误测试数据'), 'isShowError') ?>
    <?= Html::radioList('isShowError', $settings['isShowError'], [
        1 => '用户可以查看提交代码的错误测试数据',
        2 => '小组组长与管理员可查看提交代码的错误测试数据',
        0 => '只有管理员可查看提交代码的错误测试数据'

    ]) ?>
</div>



<div class="form-group">
    <?= Html::label(Yii::t('app', '答题界面'), 'showMode') ?>
    <?= Html::radioList('showMode', $settings['showMode'], [
        1 => '左右模式答题界面',
        0 => '经典模式答题界面'
    ]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '用户昵称'), 'isChangeNickName') ?>
    <?= Html::radioList('isChangeNickName', $settings['isChangeNickName'], [
        2 => '只允许修改一次',
        1 => '允许修改',
        0 => '不允许修改'
    ]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '创建小组'), 'isDefGroup') ?>
    <?= Html::radioList('isDefGroup', $settings['isDefGroup'], [

        2 => '仅管理员可创建小组',
        3 => '管理员与VIP用户可创建小组',
        1 => '所有注册用户可创建小组',
        0 => '关闭小组功能'
    ]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '组长直接加成员'), 'isGroupJoin') ?>
    <?= Html::radioList('isGroupJoin', $settings['isGroupJoin'], [
        1 => '开启',
        0 => '关闭'
    ]) ?>
    <p class="hint-block">
        开启该功能后,组长可以直接将用户拉入到小组中,不需要用户确认。
    </p>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '小组管理权限'), 'isGroupReset') ?>
    <?= Html::radioList('isGroupReset', $settings['isGroupReset'], [
        1 => '仅组长可重置密码与昵称',
        2 => '组长与管理员可重置密码与昵称',
        0 => '关闭密码与昵称重置功能'
    ]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '默认语言'), 'defaultLanguage') ?>
    <?= Html::radioList('defaultLanguage', $settings['defaultLanguage'], [
        -1 => 'All',
        0 => 'C',
        1 => 'C++',
        2 => 'Java',
        3 => 'Python3',
    ]) ?>
    <p class="hint-block">
        为新注册的用户指定默认的语言类型。
    </p>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('app', '私有(VIP)题目'), 'isHideVIP') ?>
    <?= Html::radioList('isHideVIP', $settings['isHideVIP'], [
        1 => '游客与普通用户不显示私有题目',
        0 => '所有用户都显示私有题目'
    ]) ?>
</div>


<div class="form-group">
    <?= Html::label(Yii::t('app', '编辑器'), 'ojEditor') ?>
    <?= Html::radioList('ojEditor', $settings['ojEditor'], [
        'app\widgets\ckeditor\CKeditor' => 'CKEditor编辑器',
        'app\widgets\editormd\Editormd' => 'MarkDown编辑器'
    ]) ?>
    <p class="hint-block">
        CKEditor编辑器 ：所见即所得的编辑器。
    </p>
    <p class="hint-block">
        MarkDown编辑器：支持MarkDown语法的编辑器。
    </p>
</div>



<hr>
<div class="form-horizontal">
    <h4>配置 SMTP 发送邮箱</h4>
    <p class="hint-block">
        在用户忘记密码时，需要通过此处配置的邮箱来发送"重置密码"的邮箱给用户。
        若使用默认的 "no-reply@jnoj.org"，不能保证此默认邮箱长期可用，建议自行配置自己的邮箱。
    </p>

    <div class="col-md-10 col-md-offset-1">

        <div class="form-group">
            <?= Html::radioList('mustVerifyEmail', $settings['mustVerifyEmail'], [
                1 => '新注册用户必须验证邮箱，且更改邮箱后必须验证邮箱',
                0 => '不验证'
            ]) ?>
        </div>

        <div class="form-group">
            <div class="input-group"><span class="input-group-addon">邮箱验证码有效时间</span>
                <?= Html::textInput('passwordResetTokenExpire', $settings['passwordResetTokenExpire'], ['class' => 'form-control']) ?>
            </div>
            <p class="hint-block">单位：秒。即 <?= intval($settings['passwordResetTokenExpire'] / 3600) ?> 小时后，用户邮箱确认链接失效。</p>
        </div>


        <div class="form-group">
            <div class="input-group"><span class="input-group-addon">SMTP发送服务器</span>
                <?= Html::textInput('emailHost', $settings['emailHost'], ['class' => 'form-control', 'placeholder' => 'smtp.exmail.qq.com']) ?>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group"><span class="input-group-addon">用户名</span>
                <?= Html::textInput('emailUsername', $settings['emailUsername'], ['class' => 'form-control', 'placeholder' => 'no-reply@jnoj.org']) ?>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group"><span class="input-group-addon">密码</span>
                <?= Html::textInput('emailPassword', $settings['emailPassword'], ['class' => 'form-control', 'placeholder' => 'you_password']) ?>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group"><span class="input-group-addon">端口</span>
                <?= Html::textInput('emailPort', $settings['emailPort'], ['class' => 'form-control', 'placeholder' => '465']) ?>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group"><span class="input-group-addon">加密</span>
                <?= Html::textInput('emailEncryption', $settings['emailEncryption'], ['class' => 'form-control', 'placeholder' => 'ssl']) ?>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
<div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
</div>
<?= Html::endForm(); ?>
</div>