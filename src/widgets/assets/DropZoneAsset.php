<?php
namespace verbi\yii2Helpers\widgets\assets;

use yii\web\AssetBundle;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class DropZoneAsset extends AssetBundle
{
    public $sourcePath = '@bower/dropzone/dist';

    public $js = [
        "min/dropzone.min.js"
    ];

    public $css = [
        "min/dropzone.min.css"
    ];

    public $publishOptions = [
        'forceCopy' => true,
    ];
}