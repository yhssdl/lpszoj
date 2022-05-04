<?php

/**
 * Description of KindEditor
 *
 * @author Qinn Pan <Pan JingKui, pjkui@qq.com>
 * @link http://www.pjkui.com
 * @QQ 714428042
 * @date 2015-3-4

 */

namespace app\widgets\kindeditor;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

class KindEditor extends InputWidget {

    //配置选项，参阅KindEditor官网文档(定制菜单等)
    public $clientOptions = [];
    //定义编辑器的类型，
    //默认为textEditor;
    //uploadButton：自定义上传按钮
    //dialog:弹窗
    //colorpicker:取色器
    //file-manager浏览服务器
    //image-dialog 上传图片
    //multiImageDialog批量上传图片
    //fileDialog 文件上传
    public $editorType;
    //默认配置
    protected $_options;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init() {
        $this->id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->id;
        $this->_options = [
            'uploadJson' => \yii\helpers\Url::toRoute(['/image/kindupload']),
            'autoHeightMode'=> 'true',
            'wellFormatMode' => 'false',
            'width' => '100%',
            'height' => '200',
		'items' => [
	        'source', '|',  'fontname', 'fontsize', 'forecolor', 'hilitecolor','|', 'bold','italic', 'underline','lineheight','|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|','insertorderedlist', 'insertunorderedlist','|','undo', 'redo', '|',  'image', 'table',  'emoticons',   'link', 'unlink', '|','code',
	        ],
            //'langType' => (strtolower(Yii::$app->language) == 'en-us') ? 'en' : 'zh_cn',//kindeditor支持一下语言：en,zh_CN,zh_TW,ko,ar
        ];
        
        $this->clientOptions = ArrayHelper::merge($this->_options, $this->clientOptions);
        
        if($this->hasModel()){
            parent::init();
        }
    }

    public function run() {
        $this->registerClientScript();
        if ($this->hasModel()) {
            return Html::activeTextarea($this->model, $this->attribute, ['id' => $this->id]);  
        } else {
            return Html::textarea($this->id, $this->value, ['id' => $this->id]); 
        }
    }

    /**
     * 注册客户端脚本
     */
    protected function registerClientScript() {
        KindEditorAsset::register($this->view);
        $clientOptions = Json::encode($this->clientOptions);
        $script = "KindEditor.ready(function(K) {K.create('#" . $this->id . "', " . $clientOptions . ");});";
        $this->view->registerJs($script, View::POS_END);
    }

}

?>
