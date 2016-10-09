<?php
namespace verbi\yii2Helpers\widgets\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for the Editable js files.
 * 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class RestListViewAsset extends AssetBundle
{
    public $sourcePath = '@vendor/verbi/yii2-helpers/src/widgets/assets/restListViewAssets';
    public $depends = [
        'verbi\yii2Helpers\widgets\assets\RestAsset',
    ];
    public $js = [
        'js/restListView.js',
    ];
    
    public function registerAssetFiles($view)
    {
        $result = parent::registerAssetFiles($view);
        //$view->registerJs('var oauth2 = new Oauth2(' . json_encode(['siteBaseUrl' => \Yii::$app->homeUrl,]) . ');');
        
        return $result;
    }
}
