<?php

namespace app\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/awesome-4.7.0';
    public $css = [
        'css/font-awesome.min.css',
    ];
}
