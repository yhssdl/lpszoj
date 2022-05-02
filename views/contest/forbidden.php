<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Contest;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */

$this->title = $model->title;
$this->params['model'] = $model;
?>
<?php if ($model->status == Contest::STATUS_PRIVATE) : ?>
    <h3>私密比赛</h3>
    <div class="card">
        <div class="card-body">
            该比赛仅参赛人员可见。
        </div>
    </div>
    <?php
    $this->title = '私密比赛';
    $this->params['model']->title = '';
    $this->params['model']->start_time = '';
    $this->params['model']->end_time = '';
    ?>
<?php else : ?>
    <?php
    $menuItems = [
        [
            'label' => Yii::t('app', 'Information'),
            'url' => ['contest/view', 'id' => $model->id],
            'linkOptions' => ['class' => 'active']
        ],
        [
            'label' => Yii::t('app', 'Standing'),
            'url' => Url::toRoute(['/contest/standing2', 'id' => $model->id]),
            'visible' => $model->getRunStatus() != Contest::STATUS_NOT_START
        ]
    ];
    echo Nav::widget([
        'items' => $menuItems,
        'options' => ['class' => 'nav nav-pills hidden-print'],
        'encodeLabels' => false
    ])
    ?>
    <p></p>
    <div class="row animate__animated animate__fadeIn animate__faster">
        <div class="col-lg-8">
            <div class="alert alert-light">
                <i class="fas fa-fw fa-info-circle"></i>
                <?php if (strtotime($model->end_time) >= Contest::TIME_INFINIFY) : ?>
                    <b>永久开放的题目集</b> 任何时候均可进行作答。
                <?php else : ?>
                    <b>限时开放的题目集</b> 只有在规定时间内的作答才会被计入比赛正式榜单。
                <?php endif; ?>
            </div>
            <p></p>
            <?php if ($model->description) : ?>
                <div class="card">
                    <div class="card-body" style="padding-bottom: 0.25rem;">
                        <?= Yii::$app->formatter->asMarkdown($model->description) ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="card">
                    <div class="card-body text-secondary">
                        管理员还没有上传比赛描述信息哦。
                    </div>
                </div>
            <?php endif; ?>
            <p></p>
            <?php if ($model->scenario == Contest::SCENARIO_OFFLINE) : ?>
                <div class="card">
                    <div class="card-body">
                        您尚未报名参加该比赛，请联系管理员申请参赛，或比赛结束后再来访问。
                    </div>
                </div>
                <p></p>
            <?php else : ?>
                <div class="card">
                    <div class="card-body">
                        <p>您尚未报名参加该比赛，请报名参赛或比赛结束后再来访问。</p>
                        <?php if (!Yii::$app->user->isGuest) : ?>
                            <?php if ($model->invite_code) : ?>
                                <?= Html::beginForm(['/contest/register', 'id' => $model->id, 'register' => 1], 'get') ?>

                                <?= Html::textInput('q', '', ['class' => 'form-control', 'placeholder' => '邀请码']) ?>
                                <p></p>
                                <?= Html::submitButton(Yii::t('app', '报名参赛'), ['class' => 'btn btn-success btn-block']) ?>

                                <?= Html::endForm() ?>
                            <?php else : ?>
                                <?= Html::a(Yii::t('app', '报名参赛'), ['/contest/register', 'id' => $model->id, 'register' => 1], ['class' => 'btn btn-success btn-block']) ?>
                            <?php endif; ?>
                        <?php else : ?>
                            <div class="btn btn-success btn-block disabled">请先登录</div>
                        <?php endif; ?>
                    </div>
                </div>
                <p></p>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <div class="list-group">
                <div class="list-group-item"><?= Yii::t('app', 'Current time') ?><span class="text-secondary float-right" id="nowdate"><?= date("Y-m-d H:i:s") ?></span></div>
                <div class="list-group-item"><?= Yii::t('app', 'Start time') ?><span class="text-secondary float-right"><?= $model->start_time ?></span></div>
                <div class="list-group-item"><?= Yii::t('app', 'End time') ?>
                    <?php if (strtotime($model->end_time) >= Contest::TIME_INFINIFY) : ?>
                        <span class="text-secondary float-right">一直开放</span>
                
                    <?php else : ?>
                        <span class="text-secondary float-right"><?= $model->end_time ?></span>
 
                    <?php endif; ?>
                </div>
        </div>
        <div class="list-group-item"><?= Yii::t('app', 'Type') ?><span class="text-secondary float-right"><?= $model->getType() ?></span></div>
        <div class="list-group-item"><?= Yii::t('app', 'Status') ?><span class="text-secondary float-right"><?= $model->getRunStatus(true) ?></span></div>
        </div>
    </div>

<?php endif; ?>