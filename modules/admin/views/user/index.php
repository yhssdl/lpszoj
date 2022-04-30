<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\UserSearch */

$this->title = Yii::t('app', 'Users');
?>
<div class="user-index">

    <p class="lead">管理用户信息和权限。</p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="btn-group  btn-group-justified">
        <div class="btn-group">
        <?php Modal::begin([
            'header' => '<h4>' . Yii::t('app', '批量创建用户') . '</h4>',
            'toggleButton' => ['label' => '<span class="glyphicon glyphicon-plus"></span> '.Yii::t('app', '批量创建用户'), 'class' => 'btn btn-default'],
        ]);?>
        </div>

        <?php $form = ActiveForm::begin(['options' => ['target' => '_blank']]); ?>

        <p class="hint-block">1.格式一:每个用户一行，格式为<code>用户名 密码</code>，中间用空格或Tab键分开。</p>
        <p class="hint-block">2.格式二:每个用户一行，格式为<code>用户名 昵称 密码</code>，中间用空格或Tab键分开。</p>
        <p class="hint-block">3.用户名只能以数字、字母、下划线，且非纯数字，长度在 4 - 32 位之间</p>
        <p class="hint-block">4.密码至少六位</p>

        <?= $form->field($generatorForm, 'names')->textarea(['rows' => 10])  ?>

        <p class="hint-block">请选择默认语言类型</p>
        <?= $form->field($generatorForm, 'language')->radioList( [
                0 => 'C',
                1 => 'C++',
                2 => 'Java',	            
                3 => 'Python3'	            
            ])->label(false)  ?>



        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-success  btn-block']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?php Modal::end(); ?>

        <div class="btn-group">
            <a id="general-user" class="btn btn-success" href="javascript:void(0);"><span class="glyphicon glyphicon-user"></span> 设为普通用户</a>
        </div>
        <div class="btn-group">
            <a id="vip-user" class="btn btn-success" href="javascript:void(0);"><span class="glyphicon glyphicon-fire"></span> 设为VIP用户</a>
        </div>
        <div class="btn-group">
            <a id="admin-user" class="btn btn-success" href="javascript:void(0);"><span class="glyphicon glyphicon-globe"></span> 设为管理员</a>
        </div>

    </div>
    <br>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'grid'],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
            ],
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->id, ['/user/view', 'id' => $key], ['target' => '_blank']);
                },            
                'format' => 'raw',
                'enableSorting' => false,
            ],

            [
                'attribute' => 'username',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->username, ['/user/view', 'id' => $key], ['target' => '_blank']);
                },            
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'nickname',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->nickname, ['/user/view', 'id' => $key], ['target' => '_blank']);
                },                  
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'email',
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'role',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->role == \app\models\User::ROLE_PLAYER) {
                        return '参赛用户';
                    } else if ($model->role == \app\models\User::ROLE_USER) {
                        return '普通用户';
                    } else if ($model->role == \app\models\User::ROLE_VIP) {
                        return 'VIP 用户';
                    } else if ($model->role == \app\models\User::ROLE_ADMIN) {
                        return '管理员';
                    }
                    return 'not set';
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            // 'status',
            // 'created_at',
            // 'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    $this->registerJs('
    $("#general-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "'.\yii\helpers\Url::to(['/admin/user/index', 'action' => \app\models\User::ROLE_USER]).'", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });
    $("#vip-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "'.\yii\helpers\Url::to(['/admin/user/index', 'action' => \app\models\User::ROLE_VIP]).'", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });
    $("#admin-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/user/index', 'action' => \app\models\User::ROLE_ADMIN]) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });
    ');
    ?>
</div>
