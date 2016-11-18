<?php
namespace verbi\yii2Helpers\widgets\builder;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class Form extends \kartik\builder\Form {
    use \verbi\yii2Helpers\traits\WidgetTrait;
    public $columnSize = self::SIZE_MEDIUM;
    
    public static function prepareOptions($options) {
        return $options;
    }
    
    public static function widget($options = []) {
        $options = static::prepareOptions($options);
        return parent::widget($options);
    }
}