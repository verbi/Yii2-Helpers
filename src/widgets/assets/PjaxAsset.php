<?php
namespace verbi\yii2Helpers\widgets\assets;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class PjaxAsset extends \yii\widgets\PjaxAsset {
    public $depends = [
        'yii\web\JqueryAsset',
        'verbi\yii2Helpers\widgets\assets\ScriptjsAsset',
        'yii\widgets\PjaxAsset',
    ];
    public $sourcePath = '@vendor/verbi/yii2-helpers/src/widgets/assets/pjaxAssets';
    
    public $js = [
        'js/dynamic-script-loader.js',
    ];
}