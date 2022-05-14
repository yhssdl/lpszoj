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
    $pic = Yii::getAlias('@web') . '/images/task.png';   
}



$content = '
<div class="section-body">
    <div class="media__left medium"><img class="group-img" src="'. $pic .'"></div>
    <div class="media__body medium">
        <div class="contest__title">'. $title .' </div>
        <ul class="supplementary list">
            '. $lerder_name .'
            <li>
                <span class="fa fa-user"></span> '. $user_count .' 人
            </li>
            <li>
            <span class="fa fa-rocket "></span> '. $contest_count .' 个小节
            </li>
            <li>
            <span class="fa fa-info-circle"></span> '. $description .'
            </li>
        </ul>
    </div>
</div>
';


echo Html::a($content, ['view', 'id' => $model['id']], ['class' => 'list-group-item list-group-item-action','style' => 'padding:0px;']);
?>

