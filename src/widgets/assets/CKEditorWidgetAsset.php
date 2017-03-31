<?php
namespace verbi\yii2Helpers\widgets\assets;

class CKEditorWidgetAsset extends \dosamigos\ckeditor\CKEditorWidgetAsset {
//    use \verbi\yii2Helpers\traits\assetBundles\ExcludableAssetBundleTrait {
//        getExcludeJs as private _oldExcludeJs;
//    }
//    
//    public function getExcludeJs() {
//        return true;
//    }
    
    public $depends = [
        'verbi\yii2Helpers\widgets\assets\CKEditorAsset'
    ];
}
