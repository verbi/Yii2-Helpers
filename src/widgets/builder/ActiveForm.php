<?php
namespace verbi\yii2Helpers\widgets\builder;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class ActiveForm extends \kartik\widgets\ActiveForm {
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