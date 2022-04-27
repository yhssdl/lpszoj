<?php
namespace app\widgets\codemirror;

use yii\web\AssetBundle;

/**
 * @author Shiyang <dr@shiyang.me>
 */
class CodeMirrorAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/codemirror/assets';
    public $js = [
        'lib/codemirror.js',
        'addon/selection/active-line.js',
        'addon/edit/matchbrackets.js',
        'addon/display/autorefresh.js',
        'mode/javascript/javascript.js',
        'mode/clike/clike.js',
        'mode/python/python.js'
    ];
    public $css = [
        'lib/codemirror.css',
        'theme/solarized.css',
        'theme/material.css',
        'theme/monokai.css'
    ];
    public $depends = [
    ];
}
