<?php
namespace verbi\yii2Helpers\traits;
use \yii\web\BadRequestHttpException;
use yii\web\JsExpression;
use verbi\yii2Helpers\widgets\assets\Oauth2Asset;
use yii\helpers\Json;
use yii\web\View;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
trait WidgetTrait {
    //use \kartik\base\WidgetTrait;

    /**
     * Returns the plugin registration script.
     *
     * @param string $name the name of the plugin
     * @param string $element the plugin target element
     * @param string $callback the javascript callback function to be called after plugin loads
     * @param string $callbackCon the javascript callback function to be passed to the plugin constructor
     *
     * @return string the generated plugin script
     */
    protected function getPluginScript($name, $element = null, $callback = null, $callbackCon = null)
    {
        $id = $element ? $element : "jQuery('#" . $this->options['id'] . "')";
        $script = '';
        if ($this->pluginOptions !== false) {
            $optionsScript = $this->getPluginOptionsScript($name);
            $script = "{$id}.{$name}({$this->_hashVar})";
            if ($callbackCon != null) {
                $script = "{$id}.{$name}({$this->_hashVar}, {$callbackCon})";
            }
            if ($callback != null) {
                $script = "jQuery.when({$script}).done({$callback})";
            }
            $script = $optionsScript . $script.';';
        }
        $script = $this->pluginDestroyJs . $script;
        if (!empty($this->pluginEvents)) {
            foreach ($this->pluginEvents as $event => $handler) {
                $function = $handler instanceof JsExpression ? $handler : new JsExpression($handler);
                $script .= "{$id}.on('{$event}', {$function});";
            }
        }
        return $script;
    }
    
    
    /**
     * Registers plugin options by storing within a uniquely generated javascript variable.
     *
     * @param string $name the plugin name
     */
    protected function registerPluginOptions($name)
    {
        $this->registerWidgetJs($this->getPluginOptionsScript($name), View::POS_READY);
    }
    
    protected function getPluginOptionsScript($name) {
        $this->hashPluginOptions($name);
        $encOptions = empty($this->_encOptions) ? '{}' : $this->_encOptions;
        return "var {$this->_hashVar} = {$encOptions};";
    }
    
    /**
     * Registers a specific plugin and the related events
     *
     * @param string $name the name of the plugin
     * @param string $element the plugin target element
     * @param string $callback the javascript callback function to be called after plugin loads
     * @param string $callbackCon the javascript callback function to be passed to the plugin constructor
     */
    protected function registerPlugin($name, $element = null, $callback = null, $callbackCon = null)
    {
        $script = $this->getPluginScript($name, $element, $callback, $callbackCon);
        $this->registerWidgetJs($script);
    }
}