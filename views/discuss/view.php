<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\models\Discuss */
/* @var $newDiscuss app\models\Discuss */

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problem'), 'url' => ['/problem/index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->problem->title), 'url' => ['problem/view', 'id' => $model->problem->id]];
?>
<div class="contest-form">

    <div class="alert alert-light"><?= Html::encode($model->title) ?></div>
    <p>
        <span class="fa fa-user"></span> <?= Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->user->username]) ?>
        &nbsp;•&nbsp;
        <span class="fa fa-clock-o"></span> <?= Yii::$app->formatter->asRelativeTime($model->created_at) ?>
        <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->id === $model->created_by || Yii::$app->user->identity->role == User::ROLE_ADMIN)) : ?>
            &nbsp;•&nbsp;
            <span class="glyphicon glyphicon-edit"></span> <?= Html::a(Yii::t('app', 'Edit'), ['/discuss/update', 'id' => $model->id]) ?>
            &nbsp;•&nbsp;
            <span class="fa fa-trash"></span>
            <?= Html::a(Yii::t('app', 'Delete'), ['/discuss/delete', 'id' => $model->id], [
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>
    <div class="alert alert-light"><?= Yii::$app->formatter->asMarkdown($model->content) ?></div>

    <p><?= Yii::t('app', 'Comments') ?>:</p>
    <?php foreach ($replies as $reply) : ?>
        <div class="well">
            <?= Yii::$app->formatter->asMarkdown($reply->content) ?>
            <hr>
            <span class="fa fa-user"></span> <?= Html::a(Html::encode($reply->user->nickname), ['/user/view', 'id' => $reply->user->id]) ?>
            &nbsp;•&nbsp;
            <span class="fa fa-clock-o"></span> <?= Yii::$app->formatter->asRelativeTime($reply->created_at) ?>

            <?php if (!Yii::$app->user->isGuest && (Yii::$app->user->id === $reply->created_by || Yii::$app->user->identity->role == User::ROLE_ADMIN)) : ?>
                &nbsp;•&nbsp;
                <span class="glyphicon glyphicon-edit"></span> <?= Html::a(Yii::t('app', 'Edit'), ['/discuss/update', 'id' => $reply->id]) ?>
                &nbsp;•&nbsp;
                <span class="fa fa-trash"></span>
                <?= Html::a(Yii::t('app', 'Delete'), ['/discuss/delete', 'id' => $reply->id], [
                    'data' => [
                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?= \yii\widgets\LinkPager::widget([
        'pagination' => $pages,
    ]); ?>

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($newDiscuss, 'content')->widget(Yii::$app->setting->get('ojEditor'))->label(false); ?>
    <?= Html::submitButton(Yii::t('app', 'Reply'), ['class' => 'btn btn-success btn-block']) ?>
    <?php ActiveForm::end(); ?>

</div>