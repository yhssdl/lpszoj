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
                    $pass_title = "";
                    
                    $pos = 1;
                   foreach ($trainings as $training){
                       
                        if($bShow){

                            $t_model = Training::findOne($training->id);
                            $problems = $t_model->problems;
                            $loginUserProblemSolvingStatus = $t_model->getLoginUserProblemSolvingStatus();
                            $submissionStatistics = $t_model->getSubmissionStatistics();

                            $pass_title = $t_model->title;

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

                            if($pass_sum < $training->punish_time) $bShow = false;

                            if(!$bShow && $training->enable_clarify==0){
                                echo '<br><div class="alert alert-light"><i class=" fa fa-info-circle"></i> 后续小节已经被隐藏，需要完成当前小节才能显示。</div>';
                                break;
                            }

                        }else{
                            echo $this->render('_contest_item1',
                            ['t_model' => $training,
                            'model'=>$t_model,
                            'pos' => $pos,
                            'pass_title' => $pass_title
                            ]);
                            
                        }
                       
                        $pos++;

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