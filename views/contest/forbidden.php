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
    <p class="lead">私密比赛</p>
    <div class="alert alert-light">
        <i class=" glyphicon glyphicon-info-sign"></i> 该比赛仅参赛人员可见。
    </div>
    <?php
    $this->title = '私密比赛';
    $this->params['model']->title = '';
    $this->params['model']->start_time = '';
    $this->params['model']->end_time = '';
    ?>
<?php else : ?>
    <br>
    <div class="row animate__animated animate__fadeInUp">
        <div class="col-lg-8">
            <div class="alert alert-light">
                <i class="fas fa-fw fa-info-circle"></i>
                <?php if (strtotime($model->end_time) >= Contest::TIME_INFINIFY) : ?>
                    <i class=" glyphicon glyphicon-info-sign"></i> <b>永久开放的题目集</b> 任何时候均可进行作答。
                <?php else : ?>
                    <i class=" glyphicon glyphicon-info-sign"></i> <b>限时开放的题目集</b> 只有在规定时间内的作答才会被计入比赛正式榜单。
                <?php endif; ?>
            </div>
            <p></p>
            <?php if ($model->description) : ?>
                <div class="alert alert-light">
                    <?= Yii::$app->formatter->asMarkdown($model->description) ?>
                </div>
            <?php else : ?>
                <div class="alert alert-light">
                    管理员还没有上传比赛描述信息哦。
                </div>
            <?php endif; ?>

            <?php if ($model->scenario == Contest::SCENARIO_OFFLINE) : ?>
                <div class="alert alert-light">
                    <p><i class=" glyphicon glyphicon-info-sign"></i> 该比赛必须是指定的参赛用户才能参赛，请联系管理员申请参赛，或比赛结束后再来访问。</p>
                </div>

            <?php else : ?>
                <div class="alert alert-light">
                    <p><i class=" glyphicon glyphicon-info-sign"></i> 您尚未报名参加该比赛，请报名参赛或比赛结束后再来访问。</p><br>
                    <?php if (!Yii::$app->user->isGuest) : ?>
                        <?php if ($model->invite_code) : ?>
                            <?= Html::beginForm(['/contest/register', 'id' => $model->id, 'register' => 1], 'get') ?>

                            <?= Html::textInput('q', '', ['class' => 'form-control', 'placeholder' => '邀请码']) ?>


                            <div class="btn-group btn-block">
                                <?= Html::submitButton(Yii::t('app', '报名参赛'), ['class' => 'btn btn-success']) ?>
                            </div>



                            <?= Html::endForm() ?>
                        <?php else : ?>
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <?= Html::a(Yii::t('app', '报名参赛'), ['/contest/register', 'id' => $model->id, 'register' => 1], ['class' => 'btn btn-success']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="btn btn-success btn-block disabled">请先登录</div>
                    <?php endif; ?>
                </div>
                <p></p>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <div class="list-group">

                <div class="list-group-item"><?= Yii::t('app', 'Start time') ?><span class="float-right"><?= $model->start_time ?></span></div>
                <?php if (strtotime($model->end_time) < Contest::TIME_INFINIFY) : ?>
                    <div class="list-group-item"><?= Yii::t('app', 'End time') ?>
                    <span class="float-right"><?= $model->end_time ?></span></div>
                <?php endif; ?>
                
                <div class="list-group-item">持续时间<span class="float-right"> <?= $model->getContestTimeLen() ?></span></div>
                <div class="list-group-item">参赛人数<span class="float-right"><?= $model->getContestUserCount() ?></span></div>
                <div class="list-group-item">题目数量<span class="float-right"><?= $model->getProblemCount() ?></span></div>


            </div>
            <div class="list-group-item"><?= Yii::t('app', 'Type') ?><span class="float-right"><?= $model->getType() ?></span></div>
            <div class="list-group-item"><?= Yii::t('app', 'Status') ?><span class="float-right"><?= $model->getRunStatus(1) ?></span></div>
        </div>
    </div>

<?php endif; ?>