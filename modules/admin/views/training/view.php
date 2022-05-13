<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\GroupUser;
use yii\widgets\ListView;
use app\models\Contest;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $contestDataProvider yii\data\ActiveDataProvider */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $newContest app\models\Contest */
/* @var $newGroupUser app\models\GroupUser */

$this->title = $model->name;

?>

<p class="lead"><?= $this->title ?></p>

<div class="contest-index">

    <div class="row">
        <div class="col-md-4">
            <div class=" btn-group btn-group-justified">
                <div class="btn-group">
                    <?php Modal::begin([
                        'header' => Yii::t('app', 'Create Section'),
                        'toggleButton' => [
                            'label' => '<span class="fa fa-plus"></span> ' . Yii::t('app', 'Create Section'),
                            'tag' => 'a',
                            'style' => 'cursor:pointer;',
                            'class' => 'btn btn-default '
                        ]
                    ]); ?>
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($newContest, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Title') . '</span>{input}</div>'])->textInput()->label(false) ?>

                    <?= $form->field($newContest, 'enable_clarify')->radioList([
                        0 => '未完成前隐藏下一小节',
                        1 => '显示下一小节',
                    ])->label(false) ?>

                    <?= $form->field($newContest, 'punish_time')->radioList([
                        0 => '0题',
                        1 => '1题',
                        2 => '2题',
                        3 => '3题',
                        4 => '4题',
                        5 => '5题',
                    ])->label('几题未完成即可过关') ?>

                    <?= $form->field($newContest, 'language')->radioList([
                        -1 => 'All',
                        0 => 'C',
                        1 => 'C++',
                        2 => 'Java',
                        3 => 'Python3',
                    ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>


                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4"><?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <?php Modal::end(); ?>
                </div>
                <div class="btn-group">
                    <?= Html::a(Yii::t('app', 'Setting'), ['training/update', 'id' => $model->id], ['class' => 'btn btn-success btn-block']) ?>
                </div>
            </div>
        </div>
    </div>
    <br>





    <?php if ($contestDataProvider->count > 0) {
        echo ListView::widget([
            'dataProvider' => $contestDataProvider,
            'itemView' => '_contest_item',
            'itemOptions' => ['tag' => false],
            'layout' => '{items}<p></p>{pager}',
            'options' => ['class' => 'list-group animate__animated animate__fadeInUp'],

            'pager' => [
                'firstPageLabel' => Yii::t('app', 'First'),
                'prevPageLabel' => '« ',
                'nextPageLabel' => '» ',
                'lastPageLabel' => Yii::t('app', 'Last'),
                'linkOptions' => ['class' => 'page-link'],
                'maxButtonCount' => 10,
            ]
        ]);
    } else {

        echo '<div class="alert alert-light"><i class=" fa fa-info-circle"></i> 还没有创建训练小节内容。</div>';
    }
    ?>



</div>
<?php
?>