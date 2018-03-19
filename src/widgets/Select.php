<?php
namespace verbi\yii2Helpers\widgets;
use kartik\select2\Select2;
use yii\web\JsExpression;
use verbi\yii2Helpers\widgets\assets\Oauth2Asset;
use yii\helpers\Json;
use yii\web\View;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class Select extends Select2 {
    use \verbi\yii2Helpers\traits\WidgetTrait;
    public $placeholder;
    
    public static function widget($config = []) {
       
        if(!\yii::$app->user->isGuest && isset($config['pluginOptions']) && isset($config['pluginOptions']['ajax'])) {
            if(!isset($config['pluginOptions']['ajax']['headers']) && !isset($config['pluginOptions']['ajax']['headers']['Authorization'])) {
                $config['pluginOptions']['ajax']['headers']['Authorization'] = new JsExpression(
                    '"Bearer " + (new Oauth2(' . json_encode(['siteBaseUrl' => \Yii::$app->homeUrl,]) . ')).getAjaxAccessToken()'
                );
            }
        }
        if(isset($config['placeholder'])) {
            $config['pluginOptions']['placeholder'] = $config['placeholder'];
            unset($config['placeholder']);
        }
       
        
        return parent::widget($config);
    }
    
    /**
     * Registers the client assets for [[Select2]] widget.
     */
    public function registerAssets()
    {
        $id = $this->options['id'];
        $view = $this->getView();
        Oauth2Asset::register($view);
        $this->registerAssetBundle();
        $isMultiple = $this->options['multiple'];
        $options = Json::encode([
            'themeCss' => ".select2-container--{$this->theme}",
            'sizeCss' => empty($this->addon) && $this->size !== self::MEDIUM ? 'input-' . $this->size : '',
            'doReset' => static::parseBool($this->changeOnReset),
            'doToggle' => static::parseBool($isMultiple && $this->showToggleAll),
            'doOrder' => static::parseBool($isMultiple && $this->maintainOrder),
        ]);
        $this->_s2OptionsVar = 's2options_' . hash('crc32', $options);
        $this->options['data-s2-options'] = $this->_s2OptionsVar;
        $view->registerJs("var {$this->_s2OptionsVar} = {$options};", View::POS_READY);
        if ($this->maintainOrder) {
            $val = Json::encode(is_array($this->value) ? $this->value : [$this->value]);
            $view->registerJs("initS2Order('{$id}',{$val});");
        }
        $this->registerPlugin($this->pluginName, "jQuery('#{$id}')", "initS2Loading('{$id}','{$this->_s2OptionsVar}')");
    }
}