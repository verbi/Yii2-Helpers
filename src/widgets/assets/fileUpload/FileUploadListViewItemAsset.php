<?php

namespace verbi\yii2Helpers\widgets\assets\fileUpload;

use yii\web\AssetBundle;

/**
 * Asset bundle for the Editable js files.
 * 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class FileUploadListViewItemAsset extends AssetBundle
{
    public $sourcePath = '@vendor/verbi/yii2-helpers/src/widgets/assets/fileUpload/fileUploadListViewItemAsset';

    public $css = [
        'css/fileUpload.css',
    ];
}
