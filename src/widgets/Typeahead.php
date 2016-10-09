<?php
namespace verbi\yii2Helpers\widgets;
use verbi\yii2Helpers\widgets\assets\Oauth2Asset;
use yii\web\JsExpression;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class Typeahead extends \kartik\typeahead\Typeahead {
    public static function widget($config = []) {
        if(!\yii::$app->user->isGuest && isset($config['dataset'])) {
            $dataset = [];
            foreach($config['dataset'] as $set) {
                if(isset($set['remote']) && !isset($set['remote']['prepare'])) {
                    $set['remote']['prepare'] = new JsExpression('function(query,settings){'
                        . 'var oauth2 = new Oauth2(' . json_encode(['siteBaseUrl' => \Yii::$app->homeUrl,]) .');'
                        . 'settings.headers={'
                            . '"Authorization":"Bearer " + oauth2.getAjaxAccessToken()'
                        . '};'
                        . 'settings.url=settings.url.replace("'.(isset($set['wildcard'])?$set['wildcard']:'%QUERY').'",encodeURIComponent(query));'
                        . 'return settings;'
                    . '}');
                }
                $dataset[] = $set;
            }
            $config['dataset'] = $dataset;
        }
        $return = parent::widget($config);
        return $return;
    }
    
    public function registerClientScript()
    {
        if(!\yii::$app->user->isGuest) {
            Oauth2Asset::register($this->view);
        }
        return parent::registerClientScript();
    }
}