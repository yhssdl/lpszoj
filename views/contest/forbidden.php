<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contest'), 'url' => ['/contest/index']];
$this->params['model'] = $model;
?>
<?php if ($model->status == Contest::STATUS_PRIVATE): ?>
    <h2 class="text-center">该比赛仅参赛人员可见。</h2>
    <?php
        $this->title = Yii::$app->setting->get('ojName');
        $this->params['model']->title = '';
        $this->params['model']->start_time = '';
        $this->params['model']->end_time = '';
    ?>
<?php else: ?>
    <h2 class="text-center">您尚未报名参加该比赛，请先参赛，或比赛结束后再来访问</h2>
    <hr>
    <?php if ($model->getRunStatus() == Contest::STATUS_RUNNING): ?>
        <a href="<?= Url::toRoute(['/contest/standing2', 'id' => $model->id]) ?>">
            <h3 class="text-center">查看榜单</h3>
        </a>
    <?php endif; ?>
    <?php if ($model->scenario == Contest::SCENARIO_OFFLINE): ?>
        <div class="alert alert-light"><i class=" glyphicon glyphicon-info-sign"></i> 该比赛只能由管理员指定的用户才能参赛，请联系管理员</div>
    <?php else: ?>
        <h4>参赛协议</h4>
        <p>1. 不与其他人分享解决方案</p>
        <p>2. 不以任何形式破坏和攻击测评系统</p>

        <?= Html::a(Yii::t('app', 'Agree above and register'), ['/contest/register', 'id' => $model->id, 'register' => 1]) ?>
    <?php endif; ?>
<?php endif; ?>