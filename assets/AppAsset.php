<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'js/prism/prism.css',
        'css/site.css',
        'css/animate.css',
        'js/katex/katex.min.css'
    ];
    public $js = [
        'js/katex/katex.min.js',
        'js/socket.io.js',
        'js/clipboard.min.js',
        'js/app.js',
        'js/prism/prism.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\FontAwesomeAsset',
    ];
}
