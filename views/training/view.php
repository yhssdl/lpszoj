<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $contestDataProvider yii\data\ActiveDataProvider */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $newContest app\models\Contest */
/* @var $newGroupUser app\models\GroupUser */

$this->title = $model->name;
?>

<div class="group-view">
    <div class="row">

        <div class="col-md-9">

            <?php if (Yii::$app->user->isGuest) : ?>
                <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 请登录后再来参加训练。</div>
            <?php else : ?>

                <?php if ($trainingDataProvider->count > 0) {

                    $trainings = $trainingDataProvider->getModels();
                    
                   foreach ($trainings as $training){

                        echo $this->render('_contest_item', ['model' => $training]);
                   }

                } else {
                    echo '<div class="alert alert-light"><i class=" fa fa-info-circle"></i> 组长还未创建题目。</div>';
                }
                ?>
            <?php endif; ?>
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

</div>
<?php
?>