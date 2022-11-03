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

    <p class="lead">管理用户信息和权限</p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <br>

    <div class="btn-group  btn-group-justified">
        <div class="btn-group">
        <?php Modal::begin([
            'header' => Yii::t('app', '批量创建用户'),
            'toggleButton' => ['label' => '<span class="fa fa-plus"></span> '.Yii::t('app', '批量创建用户'), 'class' => 'btn btn-default'],
        ]);?>


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
        <div class="row"><div class="col-md-4 col-md-offset-4"><?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-success  btn-block']) ?></div></div>
        </div>

        <?php ActiveForm::end(); ?>
        <?php Modal::end(); ?>
        </div>
        <div class="btn-group">
            <a id="general-user" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="将选中的用户设置为普通用户"><span class="fa fa-user"></span> 设为普通用户</a>
        </div>
        <div class="btn-group">
            <a id="vip-user" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="将选中的用户设置为VIP用户"><span class="fa fa-key"></span> 设为VIP用户</a>
        </div>
        <div class="btn-group">
            <a id="admin-user" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="将选中的用户设置为管理员"><span class="fa fa-globe"></span> 设为管理员</a>
        </div>
        <div class="btn-group">
            <a id="enable-user" class="btn btn-primary" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="启用选中的用户"><span class="fa fa-user"></span> 启用账户</a>
        </div>
        <div class="btn-group">
            <a id="disable-user" class="btn btn-primary" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="禁用选中的用户"><span class="fa fa-user-times"></span> 禁用账户</a>
        </div>        
        <div class="btn-group">
            <a id="delete" class="btn btn-danger" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="删除选中用户，不可恢复"><span class="fa fa-trash"></span> 删除</a>
        </div>

    </div>
    <br>
    <?= GridView::widget([
        'layout' => '{items}{summary}{pager}',
        'pager' =>[
            'firstPageLabel' => Yii::t('app', 'First'),
            'prevPageLabel' => '« ',
            'nextPageLabel' => '» ',
            'lastPageLabel' => Yii::t('app', 'Last'),
            'maxButtonCount' => 10
        ],
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },
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
                    
                    $s_str="";
                    $user_str = 'not set';
                    $e_str = "<span>";

                   if ($model->role == \app\models\User::ROLE_PLAYER) {
                        $s_str = "<span class='text-primary'>";
                        $icon = '<span class="fa fa-vcard"></span> ';
                        $user_str =  '参赛用户';
                    } else if ($model->role == \app\models\User::ROLE_USER) {
                        $icon = '<span class="fa fa-user"></span> ';
                        $user_str =  '普通用户';
                    } else if ($model->role == \app\models\User::ROLE_VIP) {
                        $s_str = "<span class='text-success'>";
                        $icon = '<span class="fa fa-user-plus"></span> ';
                        $user_str =  'VIP 用户';
                    } else if ($model->role == \app\models\User::ROLE_ADMIN) {
                        $s_str = "<span class='text-info'>";
                        $icon = '<span class="fa fa-user-secret"></span> ';
                        $user_str =  '管理员';
                    }

                    if($model->status == \app\models\User::STATUS_DISABLE) {
                        $s_str = "<span title='已经被禁用的用户' class='text-danger'>";
                        $icon = '<span><span class="fa fa-lock"></span> ';  
                    } 
                    return $s_str.$icon.$user_str.$e_str;
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'memo',
                'value' => function ($model, $key, $index, $column) {
                    if($model->memo==null) return "";
                    return $model->memo;
                },
                'format' => 'raw',
                'enableSorting' => false
            ],
            // 'status',
            // 'created_at',
            // 'updated_at',
            ['class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['class'=>'a_just']
            ],
        ],
    ]);
    $this->registerJs('
    $(function () {
        $(\'[data-toggle="tooltip"]\').tooltip()
      })
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

    $("#enable-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/user/index', 'action' => 'enable']) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });

    $("#disable-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/user/index', 'action' => 'disable']) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });    


    $("#delete").on("click", function () {
        if (confirm("确定要删除？此操作不可恢复！")) {
            var keys = $("#grid").yiiGridView("getSelectedRows");
            $.post({
               url: "' . \yii\helpers\Url::to(['/admin/user/index', 'action' => 'delete']) . '", 
               dataType: \'json\',
               data: {keylist: keys}
            });
        }
    });

    ');
    ?>
</div>
