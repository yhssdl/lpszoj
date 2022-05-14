<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\models\Group;
use app\models\GroupUser;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $userDataProvider yii\data\ActiveDataProvider */

$this->title = Html::encode($model->name);
?>
<div class="row">
    <div class="col-md-9">
        <p class="lead"><?= Html::a(Html::encode($model->name), ['/group/view', 'id' => $model->id]) ?></p>
        <?php if (Yii::$app->user->isGuest) : ?>
            <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 请先登录后再尝试参加训练。</div>
        <?php else : ?>
            <div class="row">
                <div class="col-md-3"><?= Html::a('参加训练', ['accept', 'id' => $model->id, 'accept' => 1], ['class' => 'btn btn-success btn-block']) ?></div>
            </div>
    </div>
    <div class="col-md-3">
            <div>
                <?php if ($model->kanban) : ?>
                    <div class="list-group-item list-group-item-action"><?= Yii::$app->formatter->asMarkdown($model->kanban) ?></div><br>
                <?php endif; ?>
            </div>
            <div class="list-group">
                <div class="list-group-item"><?= Yii::t('app', '参与人数') ?><span class="float-right"> <?= $model->getGroupUserCount() ?></span></div>
                <div class="list-group-item"><?= Yii::t('app', '小节数量') ?><span class="float-right"> <?= $model->getContestCount() ?></span></div>
            </div>


        </div>

</div>


<?php endif; ?>