<?php

namespace verbi\yii2Helpers\widgets\assets\fileUpload;

use yii\web\AssetBundle;
//use limion\jqueryfileupload\jQueryFileUploadPlusUIAsset;

/**
 * Asset bundle for the Editable js files.
 * 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class FileUploadPlusUIAsset extends AssetBundle//\limion\jqueryfileupload\jQueryFileUploadPlusUIAsset
{
    public $sourcePath = '@vendor/verbi/yii2-helpers/src/widgets/assets/fileUpload/fileUploadAsset';

//    public $css = [
//        'css/fileUpload.css',
//    ];
    
    public $depends = [
        '\limion\jqueryfileupload\JQueryFileUploadPlusUIAsset',
//        jQueryFileUploadPlusUIAsset::className,
    ];
    
    public $js = [
        'js/jquery.fileupload-ui.js'
    ];
}
