<?php
/* @var $users app\models\ContestUser */
?>
<head>
<style>

table {
    border-top: 1px solid #999;
    border-left: 1px solid #999;
    border-spacing: 0;
    margin: auto;
}

table  td{
    border-bottom: 1px solid #999;
    border-right: 1px solid #999;
    padding: 10px 30px;
    text-align: center;
}


</style>
</head>
<table>
    <tbody>
    <tr><td colspan="3"><?= Yii::t('app',"Copy these accounts to distribute") ?></td></tr>
    <tr>
        <td><?= Yii::t('app',"Username") ?></td>
        <td><?= Yii::t('app',"Nickname") ?></td>
        <td><?= Yii::t('app',"Password") ?></td>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user->user->username ?></td>
            <td><?= $user->user->nickname ?></td>
            <td><?= $user->user_password ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
