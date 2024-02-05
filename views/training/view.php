<?php
/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $contestDataProvider yii\data\ActiveDataProvider */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $newContest app\models\Contest */
/* @var $newGroupUser app\models\GroupUser */
use app\models\Training;
use yii\bootstrap\Nav;
use app\models\User;

$this->title = $model->name;
?>
<?php 
if(!Yii::$app->user->isGuest && Yii::$app->user->identity->role == User::ROLE_ADMIN){
   echo Nav::widget([
        'items' => [
            [
                'label' => $this->title,
                'url' => ['training/view', 'id' => $model->id]
            ],
            [
                'label' => Yii::t('app', 'Member'),
                'url' => ['training/user', 'id' => $model->id]
            ],
        ],
        'options' => ['class' => 'nav-tabs']
    ]);
}
 ?>
<br>
<div class="group-view">
    <div class="row">

        <div class="col-md-9">

            <?php if (Yii::$app->user->isGuest) : ?>
                <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 请登录后再来参加训练。</div>
            <?php else : ?>

                <?php if ($trainingDataProvider->count > 0) {

                    if(!Yii::$app->user->isGuest && Yii::$app->user->identity->role >= User::ROLE_TEACHER){
                        echo '<br><div class="alert alert-light"><i class=" fa fa-info-circle"></i> 当前为管理员或教师账号，可直接查看所有小节。</div>';
                    }

                    $trainings = $trainingDataProvider->getModels();
                    $bShow = true;
                    $pass_title = "";
                    
                    $pos = count($trainings);
                    foreach ($trainings as $training){
                        if($bShow){
                            $t_model = Training::findOne($training->id); 
                            $pass_title = $t_model->title;
                            $problems = $t_model->problems;
                            $loginUserProblemSolvingStatus = $t_model->getLoginUserProblemSolvingStatus();
                            $submissionStatistics = $t_model->getSubmissionStatistics();
                        
                            $sum = count($problems);
                            $pass_sum = 0;
                            foreach ($problems as $key => $p){
                                if(isset($loginUserProblemSolvingStatus[$p['problem_id']])){
                                    if ($loginUserProblemSolvingStatus[$p['problem_id']] == \app\models\Solution::OJ_AC){
                                        $pass_sum++;
                                    }                                   
                                }
                            }
                            if($training->punish_time<0) $training->punish_time = $sum;
                            $bPass = false;
                            if($pass_sum >= $training->punish_time) $bPass = true;



                            echo $this->render('_contest_item',
                            ['t_model' => $training,
                            'model'=>$t_model,
                            'loginUserProblemSolvingStatus' => $loginUserProblemSolvingStatus,
                            'submissionStatistics' => $submissionStatistics,
                            'problemSum' => $sum,
                            'passProblem' => $pass_sum,
                            'pass' => $bPass,
                            'pos' => $pos
                            ]);

                            if($pass_sum < $training->punish_time && (Yii::$app->user->isGuest || Yii::$app->user->identity->role < User::ROLE_TEACHER)) $bShow = false;

                           
                            if(!$bShow && $training->enable_clarify==0 && $pos>1){
                                echo '<br><div class="alert alert-light"><i class=" fa fa-info-circle"></i> 后续小节已经被隐藏，需要完成前一小节才能显示。</div>';
                                break;
                            }

                        }else{
                            echo $this->render('_contest_item1',
                            ['t_model' => $training,
                            'pos' => $pos,
                            'pass_title' => $pass_title
                            ]);

                            if($training->enable_clarify==0 && $pos>1){
                                echo '<br><div class="alert alert-light"><i class=" fa fa-info-circle"></i> 后续小节已经被隐藏，需要完成前一小节才能显示。</div>';
                                break;
                            }                            
                        }
                        $pos--;

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
$js = <<<EOF
    $('.panel-collapse ').on('show.bs.collapse', function () {
        $(this).prev(".panel-heading").find(".openswitch").html("<span class='fa fa-angle-double-up' title='收起'></span>");
    });

    $('.panel-collapse ').on('hide.bs.collapse', function () {
        $(this).prev(".panel-heading").find(".openswitch").html("<span class='fa fa-angle-double-down' title='展开'></span>");
    });
EOF;
    $this->registerJs($js);
?>