<?php
namespace verbi\yii2Helpers\widgets\assets;

use yii\web\AssetBundle;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class PjaxPatchAsset extends AssetBundle {
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $sourcePath = '@vendor/verbi/yii2-helpers/src/widgets/assets/pjaxPatchAssets';
    
    public $js = [
        'js/patch.js',
    ];
}