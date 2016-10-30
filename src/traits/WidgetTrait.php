<?php
namespace verbi\yii2Helpers\traits;
use \yii\web\BadRequestHttpException;


/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
trait WidgetTrait {
    use \kartik\base\WidgetTrait;

    protected function getPluginOptions($name) {
        $this->hashPluginOptions($name);
        $encOptions = empty($this->_encOptions) ? '{}' : $this->_encOptions;
        return $encOptions;
    }

    protected function getPluginScript($name, $element = null, $callback = null, $callbackCon = null)
    {
        $script = parent::getPluginScript($name, $element, $callback, $callbackCon);
        $encOptions = $this->getPluginOptions($name);
        return "var {$this->_hashVar} = {$encOptions};\n".$script;
    }
}