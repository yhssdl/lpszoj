<?php
/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $contestDataProvider yii\data\ActiveDataProvider */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $newContest app\models\Contest */
/* @var $newGroupUser app\models\GroupUser */
use app\models\Training;

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
                    $bShow = true;
                    
                   foreach ($trainings as $training){

                        if($bShow){
                            $t_model = Training::findOne($training->id);
                            $problems = $t_model->problems;
                            $loginUserProblemSolvingStatus = $t_model->getLoginUserProblemSolvingStatus();
                            $submissionStatistics = $t_model->getSubmissionStatistics();
                            echo $this->render('_contest_item',
                            ['t_model' => $training,
                            'model'=>$t_model,
                            'loginUserProblemSolvingStatus' => $loginUserProblemSolvingStatus,
                            'submissionStatistics' => $submissionStatistics
                            ]);
                            $sum = count($problems);
                            $pass_sum = 0;
                            foreach ($problems as $key => $p){
                                if ($loginUserProblemSolvingStatus[$p['problem_id']] == \app\models\Solution::OJ_AC){
                                    $pass_sum++;
                                }
                            }

                            if($training->punish_time<0) $training->punish_time = $sum;
                            if($pass_sum < $training->punish_time) $bShow = false;

                            if(!$bShow && $training->enable_clarify==0){
                                echo '<br><div class="alert alert-light"><i class=" fa fa-info-circle"></i> 后续小节已经被隐藏，需要完成当前小节才能显示。</div>';
                                break;
                            }

                        }else{
                            echo $this->render('_contest_item1',
                            ['t_model' => $training
                            ]);
                            
                        }
                        echo "<br>";






                   }
                  

                } else {
                    echo '<div class="alert alert-light"><i class=" fa fa-info-circle"></i> 当前训练还没有添加题目。</div>';
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