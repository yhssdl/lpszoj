<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $form yii\widgets\ActiveForm */

$scoreboardFrozenTime = Yii::$app->setting->get('scoreboardFrozenTime') / 3600;
?>

<div class="contest-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 比赛名称应该包含年份、序号、是否重现赛等信息。</div>
    <?= $form->field($model, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Title') . '</span>{input}</div>'])->textInput() ?>



    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 按需填写。以 <code>https://</code> 开头。从比赛列表访问此比赛将重定向至此链接。填写此项将使本比赛的问题列表、答疑、榜单公告等功能失效。</div>

    <?= $form->field($model, 'ext_link', ['template' => '<div class="input-group"><span class="input-group-addon">站外比赛链接</span>{input}</div>'])->textInput() ?>


    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 按需填写。若同时填写站外比赛一栏，邀请码将马上在前台（比赛列表）展示，适合指引用户参加 vjudge 私有比赛等场景；
        如果站外比赛一栏留空，邀请码将被用作普通的比赛密码使用，即不在前台展示，对于非小组比赛，用户需要填写与此相同的邀请码才可注册参赛，适合线下赛等场景（赛后无需邀请码即可看题交题）。</div>

    <?= $form->field($model, 'invite_code', ['template' => '<div class="input-group"><span class="input-group-addon">邀请码</span>{input}</div>'])->textInput() ?>


    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 如需启用永久题目集，结束时间设置为 9999 年任意一天即可，直接按格式填写日期，选单是选不了这个日期的。</div>


    <?= $form->field($model, 'start_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Start Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
        'clientOptions' => [
            'istoday' => true,
            'type' => 'datetime'
        ],
        'options' => ['autocomplete' => 'off']
    ]) ?>

    <?= $form->field($model, 'end_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'End Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
        'clientOptions' => [
            'istoday' => true,
            'type' => 'datetime'
        ],
        'options' => ['autocomplete' => 'off']
    ]) ?>

    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 封榜仅对 ACM/ICPC 或作业有效，请不要在其它赛制启用，否则可能出现未知行为。如果不需要封榜请留空，当前会在比赛结束 <?= $scoreboardFrozenTime ?> 小时后才会自动在前台页面解除封榜限制。如需提前结束封榜也可选择清空该表单项。使用封榜功能，后台管理界面的比赛榜单仍然处于实时榜单。</div>


    <?= $form->field($model, 'lock_board_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Lock Board Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
        'clientOptions' => [
            'istoday' => true,
            'type' => 'datetime'
        ]
    ]) ?>

    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 设置比赛的罚时（分钟），仅在 ACM/ICPC 赛制生效，不填则默认 20 分钟。</div>
    <?= $form->field($model, 'punish_time', ['template' => '<div class="input-group"><span class="input-group-addon">罚时</span>{input}</div>'])->textInput() ?>


    <?= $form->field($model, 'status')->radioList([
        Contest::STATUS_HIDDEN => Yii::t('app', 'Hidden'),
        Contest::STATUS_VISIBLE => Yii::t('app', 'Public'),
        Contest::STATUS_PRIVATE => Yii::t('app', 'Private'),
    ])->hint('公开：任何用户均可参加比赛（专用赛场景除外）。私有：任何时候比赛均只能由参赛用户访问，且比赛用户需要在后台手动添加。隐藏：前台无法看到比赛') ?>


    <?= $form->field($model, 'enable_print')->radioList([
        '0' => '关闭',
        '1' => '开启',
    ]) ?>

    <?= $form->field($model, 'enable_clarify')->radioList([
        0 => '关闭',
        1 => '开启',
    ])->hint('答疑界面在比赛中可以根据需要开启或关闭。') ?>

    <?= $form->field($model, 'enable_board')->radioList([
        '0' => '关闭',
        '1' => '开启',
    ])->hint('是否开启榜单功能。关闭榜单后，只能在榜单中看到自己的信息。') ?>

    <?= $form->field($model, 'scenario')->radioList([
        $model::SCENARIO_ONLINE => Yii::t('app', 'Online'),
        $model::SCENARIO_OFFLINE => Yii::t('app', 'Offline'),
    ])->hint('') ?>

    <?= $form->field($model, 'type')->radioList([
        Contest::TYPE_RANK_SINGLE => Yii::t('app', 'Single Ranked'),
        Contest::TYPE_RANK_GROUP => Yii::t('app', 'ACM/ICPC'),
        Contest::TYPE_HOMEWORK => Yii::t('app', 'Homework'),
        Contest::TYPE_OI => Yii::t('app', 'OI'),
        Contest::TYPE_IOI => Yii::t('app', 'IOI'),
    ])->hint('不同类型的区别只在于榜单的排名方式。如需使用OI比赛，请在后台设置页面启用OI模式。 <a href="/wiki/index#sz" target="_blank">点击查看赛制类型</a>') ?>


    <?= $form->field($model, 'language')->radioList([
        -1 => 'All',
        0 => 'C',
        1 => 'C++',
        2 => 'Java',
        3 => 'Python3',
    ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>


    <?= $form->field($model, 'description')->widget(Yii::$app->setting->get('ojEditor'))->label(); ?>
    <div class="form-group">
    <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>