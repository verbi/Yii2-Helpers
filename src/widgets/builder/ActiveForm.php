<?php
namespace verbi\yii2Helpers\widgets\builder;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class ActiveForm extends \kartik\widgets\ActiveForm {
    use \verbi\yii2Helpers\traits\WidgetTrait;
    protected static function prepareOptions($options) {
        $formOptions = [
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
        ];

        return array_merge($formOptions, $options);
    }
    
    public static function begin($options= []) {
        static::prepareOptions($options);
        return parent::begin($options);
    }
}