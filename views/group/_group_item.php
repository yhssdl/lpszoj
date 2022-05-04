<?php

use yii\helpers\Html;
use app\models\Group;

$description = $model['description'];

if(!$description) $description = "该小组没有任何描述";
$description = Html::encode($description);
$title = Html::encode($model['name']);

$pic = '<img src="'. Yii::getAlias('@web') . '/images/group.png" width="64px">';

if($model['join_policy']==Group::JOIN_POLICY_INVITE) $join = '<span class="contest-tag status-ended"><span class="glyphicon glyphicon-lock"></span> 私有小组</span>';
else if($model['join_policy']==Group::JOIN_POLICY_APPLICATION) $join = '<span class="contest-tag contest-tag-blue"><span class="glyphicon glyphicon-lock"></span> 普通小组</span>';
else $join = '<span class="contest-tag contest-tag-green"><span class="glyphicon glyphicon-lock"></span> 公开小组</span>';

$content = '
<div class="section-body">
    <div class="media__left medium">'. $pic .'</div>
    <div class="media__body medium">
        <div class="contest__title">'. $title .' </div>
        <ul class="supplementary list">
            <li>
                '.$join.'
            </li>

            <li>
                <span class="glyphicon glyphicon-info-sign"></span> '. $description .'
            </li>
        </ul>
    </div>
</div>
';


echo Html::a($content, ['/group/view', 'id' => $model['id']], ['class' => 'list-group-item list-group-item-action','style' => 'padding:0px;']);
?>

