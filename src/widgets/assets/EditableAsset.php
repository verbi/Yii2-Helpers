<?php
namespace verbi\yii2Helpers\widgets\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for the Editable js files.
 * 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class EditableAsset extends AssetBundle
{
    public $sourcePath = '@vendor/verbi/yii2-helpers/src/widgets/assets/editableAssets';
    /*public $css = [
        'bootstrap-social.css',
    ];*/
    public $js = [
        'js/editable.js',
    ];
}
