<?php

namespace verbi\yii2Helpers\widgets\assets;
use yii\web\AssetBundle;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class NProgressAsset extends AssetBundle
{
    public $sourcePath = '@bower/nprogress';
    public $js = [
        'nprogress.js'
    ];
    public $css = [
        'nprogress.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}