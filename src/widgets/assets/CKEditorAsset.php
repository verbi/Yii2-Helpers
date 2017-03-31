<?php

namespace verbi\yii2Helpers\widgets\assets;

use yii\web\AssetBundle;


class CKEditorAsset extends \dosamigos\ckeditor\CKEditorAsset
{
    use \verbi\yii2Helpers\traits\assetBundles\ExcludableAssetBundleTrait {
        getExcludeJs as private _oldExcludeJs;
    }
    
    public function getExcludeJs() {
        return true;
    }
    
    public $js = [
        'ckeditor.js',
//        'adapters/jquery.js'
    ];
}