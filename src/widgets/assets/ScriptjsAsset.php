<?php
namespace verbi\yii2Helpers\widgets\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for the ajax js files.
 * 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class ScriptjsAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset'
    ];
    public $sourcePath = '@vendor/bower/scriptjs/src';
    public $js = [
        'script.js',
    ];
}
