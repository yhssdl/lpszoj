<?php

use yii\helpers\Html;
use app\models\Group;

$description = $model['description'];

if(!$description) $description = "该小组没有任何描述";
$description = Html::encode($description);
$title = Html::encode($model['name']);

$g_model = Group::findOne($model['id']);

$user_count = $g_model->getGroupUserCount();
$contest_count = $g_model->getContestCount();


$lerder =  $g_model->getLeader();
if($lerder) {
    $lerder_name = '<li><span class="contest-tag contest-tag-info"><span class="fa fa-user-o"></span> 组长:'. $lerder->nickname.'</span></li>';
} else {
    $lerder_name = '';
}

if($model['logo_url']){
    $pic = $model['logo_url'];
}else{
    $pic = Yii::getAlias('@web') . '/images/group.png';   
}



if($model['join_policy']==Group::JOIN_POLICY_INVITE) $join = '<span class="contest-tag status-ended"><span class="fa fa-lock"></span> 私有小组</span>';
else if($model['join_policy']==Group::JOIN_POLICY_APPLICATION) $join = '<span class="contest-tag contest-tag-blue"><span class="fa fa-street-view"></span> 普通小组</span>';
else $join = '<span class="contest-tag contest-tag-green"><span class="fa fa-key"></span> 公开小组</span>';

$content = '
<div class="section-body">
    <div class="media__left medium"><img class="group-img" src="'. $pic .'"></div>
    <div class="media__body medium">
        <div class="contest__title">'. $title .' </div>
        <ul class="supplementary list">
            <li>
                '.$join.'
            </li>
            '. $lerder_name .'
            <li>
                <span class="fa fa-user"></span> '. $user_count .' 人
            </li>
            <li>
            <span class="fa fa-rocket "></span> '. $contest_count .' 个任务
            </li>
            <li>
            <span class="fa fa-info-circle"></span> '. $description .'
            </li>
        </ul>
    </div>
</div>
';


echo Html::a($content, ['/group/view', 'id' => $model['id']], ['class' => 'list-group-item list-group-item-action','style' => 'padding:0px;']);
?>

