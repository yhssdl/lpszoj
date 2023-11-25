<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\UserSearch */

$this->title = Yii::t('app', 'Users');
if(isset($_GET["page"]))    
    $page = $_GET["page"];
else
    $page = 1;
    
if(isset($_GET["per-page"]))  
    $perpage = $_GET["per-page"];
else
    $perpage = 50;

$js = <<<EOT
    function set_cookie(cookie_name,val) {
        var expires = new Date();
        expires.setTime(expires.getTime() + 3650 * 30 * 24 * 60 * 60 * 1000);
        document.cookie = cookie_name + "=" + val + ";expires=" + expires.toGMTString();
    }

    $("#showNickName").click(function () {
        set_cookie('showNickName',0);
        window.location.reload();
    });
    $("#showEmail").click(function () {
        set_cookie('showEmail',0);
        window.location.reload();
    });
    $("#showMemo").click(function () {
        set_cookie('showMemo',0);
        window.location.reload();
    });
    $("#showCreated_at").click(function () {
        set_cookie('showCreated_at',0);
        window.location.reload();
    });  
    $("#showSchool").click(function () {
        set_cookie('showSchool',0);
        window.location.reload();
    });        
    $("#showAll").click(function () {
        set_cookie('showNickName',1);
        set_cookie('showEmail',1);
        set_cookie('showMemo',1);
        set_cookie('showCreated_at',1);
        set_cookie('showSchool',1);
        window.location.reload();
    });
EOT;
$this->registerJs($js);   

if(isset($_COOKIE['showNickName']))
    $showNickName = $_COOKIE['showNickName'];
else 
    $showNickName = 1;

if(isset($_COOKIE['showEmail']))
    $showEmail = $_COOKIE['showEmail'];
else 
    $showEmail = 0;    

if(isset($_COOKIE['showMemo']))
    $showMemo = $_COOKIE['showMemo'];
else 
    $showMemo = 1;    

if(isset($_COOKIE['showCreated_at']))
    $showCreated_at = $_COOKIE['showCreated_at'];
else 
    $showCreated_at = 0;  

if(isset($_COOKIE['showSchool']))
    $showSchool = $_COOKIE['showSchool'];
else 
    $showSchool = 0;      
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

            <p class="hint-block">1.格式一:每个用户一行，格式为<code>用户名 密码</code></p>
            <p class="hint-block">2.格式二:每个用户一行，格式为<code>用户名 昵称 密码</code></p>
            <p class="hint-block">3.格式三:每个用户一行，格式为<code>用户名 昵称 密码 学校名</code></p>
            <p class="hint-block">3.格式四:每个用户一行，格式为<code>用户名 昵称 密码 学校名 备注</code></p>         
            <p class="hint-block">4.用户名长度在 4-32 位之间，密码至少六位，中间用空格或Tab键分开。</p>
            <?= $form->field($generatorForm, 'names')->textarea(['rows' => 10]) ?>
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
            <?php Modal::begin([
                'header' => Yii::t('app', '批量设置属性'),
                'toggleButton' => ['title'=>'批量修改选中用户的密码、昵称、备注、角色等。','label' => '<span class="fa fa-edit"></span> '.Yii::t('app', '批量设置属性'), 'class' => 'btn btn-success'],
            ]);?>

            <?= Html::beginForm(['user/index', 'action'=> 'setuser'], 'post',['id' => 'form-setuser']) ?>
            <div class="form-group">
                <div class="input-group"><span class="input-group-addon">新密码</span>
                    <?= Html::textInput('newPassword', '', ['class' => 'form-control']) ?>
                </div>
                <p class="hint-block">留空不进行密码修改。</p>
            </div>

            <div class="form-group">
                <div class="input-group"><span class="input-group-addon">新昵称</span>
                    <?= Html::textInput('nickname', '', ['class' => 'form-control']) ?>
                </div>
                <p class="hint-block">{u}表示用户名,{n}表示原昵称，示例：<code>{u}{n}</code>表示使用用户名与旧昵称形成新昵称。</p>
            </div>

            <div class="form-group">
                <div class="input-group"><span class="input-group-addon">学校</span>
                    <?= Html::textInput('school', '', ['class' => 'form-control']) ?>
                </div>
                <p class="hint-block">留空不进行学校名称修改。</p>
            </div>

            <div class="form-group">
                <div class="input-group"><span class="input-group-addon">备注</span>
                    <?= Html::textInput('memo', '', ['class' => 'form-control']) ?>
                </div>
                <p class="hint-block">留空不进行备注修改。</p>
            </div>

            <div class="form-group">
                <?= Html::label(Yii::t('app', 'Role'), 'role') ?>
                <?= Html::radioList('role', '', [
                 User::ROLE_PLAYER => '参赛用户',
                 User::ROLE_USER => '普通用户',
                 User::ROLE_VIP => 'VIP用户',
                 User::ROLE_ADMIN => '管理员'
                ]) ?>
                <p class="hint-block">
                    不选择就保留原来的角色。
                </p>
            </div>

            <div class="form-group">
            <div class="row"><div class="col-md-4 col-md-offset-4"><?= Html::Button(Yii::t('app', 'Ok'), ['class' => 'btn btn-success  btn-block','id' => 'bt_setuser']) ?></div></div>
            </div>

            <?= Html::endForm(); ?>
            <?php Modal::end(); ?>
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
                    if($model->status == \app\models\User::STATUS_DISABLE)
                        return Html::a($model->id, ['/user/view', 'id' => $key],['class'=>'text-gray'], ['target' => '_blank']);
                    else
                        return Html::a($model->id, ['/user/view', 'id' => $key], ['target' => '_blank']);
                },            
                'format' => 'raw',
                'enableSorting' => false,
            ],

            [
                'attribute' => 'username',
                'value' => function ($model, $key, $index, $column) {
                    if($model->status == \app\models\User::STATUS_DISABLE)
                        return Html::a($model->username, ['/user/view', 'id' => $key],['class'=>'text-gray'], ['target' => '_blank']);
                    else
                        return Html::a($model->username, ['/user/view', 'id' => $key], ['target' => '_blank']);
                },            
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'nickname',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showNickName', $showNickName, ['id' => 'showNickName','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Nickname')."</label>",
                'value' => function ($model, $key, $index, $column) {
                    if($model->status == \app\models\User::STATUS_DISABLE)
                        return Html::a($model->nickname, ['/user/view', 'id' => $key],['class'=>'text-gray'], ['target' => '_blank']);
                    else
                        return Html::a($model->nickname, ['/user/view', 'id' => $key], ['target' => '_blank']);
                },                  
                'format' => 'raw',
                'enableSorting' => false,
                'visible' =>  $showNickName==1,
            ],
            [
                'attribute' => 'school',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showSchool', $showSchool, ['id' => 'showSchool','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'School')."</label>",
                'value' => function ($model, $key, $index, $column) {
                    return $model->profile->school;
                },                  
                'format' => 'raw',
                'enableSorting' => false,
                'visible' =>  $showSchool==1,
            ],            
            [
                'attribute' => 'email',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showEmail', $showEmail, ['id' => 'showEmail','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Email')."</label>",
                'format' => 'raw',
                'enableSorting' => false,
                'visible' =>  $showEmail==1,
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
                        $s_str = "<span title='该用户已经被禁用' class='text-danger'>";
                        $icon = '<span><span class="fa fa-lock"></span> ';  
                    } 
                    return $s_str.$icon.$user_str.$e_str;
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            // 'status',
            [
                'attribute' => 'created_at',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showCreated_at', $showCreated_at, ['id' => 'showCreated_at','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Created At')."</label>",
                'format' => 'raw',
                'enableSorting' => false,
                'visible' =>  $showCreated_at==1,
            ],
            // 'updated_at',
            ['class' => 'yii\grid\ActionColumn',
            'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showAll', 0, ['id' => 'showAll','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Show all')."</label>",
            'contentOptions' => ['class'=>'a_just']
            ],
            [
                'attribute' => 'memo',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showMemo', $showMemo, ['id' => 'showMemo','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Memo')."</label>",
                'value' => function ($model, $key, $index, $column) {
                    if($model->memo==null) return "";
                    return $model->memo;
                },
                'format' => 'raw',
                'enableSorting' => false,
                'visible' =>  $showMemo==1,
            ],    
        ],
    ]);
    $this->registerJs('
    $(function () {
        $(\'[data-toggle="tooltip"]\').tooltip()
      })

    $("#enable-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/user/index', 'action' => 'enable','page' => $page,'per-page' => $perpage]) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });

    $("#disable-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/user/index', 'action' => 'disable','page' => $page,'per-page' => $perpage]) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });    
    $("#bt_setuser").on("click", function () {
        var passVal = $("input[name=\'newPassword\']").val();
        var nickVal = $("input[name=\'nickname\']").val();
        var memoVal = $("input[name=\'memo\']").val();
        var roleVal = $("input[name=\'role\']:checked").val();
        var schoolVal = $("input[name=\'school\']").val();
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
            url: "' . \yii\helpers\Url::to(['/admin/user/index', 'action' => 'setuser','page' => $page,'per-page' => $perpage]) . '", 
            dataType: \'json\',
            data: {keylist: keys,newPassword:passVal,nickname:nickVal,memo:memoVal,school:schoolVal,role:roleVal}
         });
    });

    $("#delete").on("click", function () {
        if (confirm("确定要删除？此操作不可恢复！")) {
            var keys = $("#grid").yiiGridView("getSelectedRows");
            $.post({
               url: "' . \yii\helpers\Url::to(['/admin/user/index', 'action' => 'delete','page' => $page,'per-page' => $perpage]) . '", 
               dataType: \'json\',
               data: {keylist: keys}
            });
        }
    });

    ');
    ?>
</div>
