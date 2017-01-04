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
        if (isset($options['usePlaceHolders'])) {
            if ($options['usePlaceHolders'] && isset($options['attributes']) && is_array($options['attributes'])) {
                $model = isset($options['model'])?$options['model']:null;
                array_walk($options['attributes'], function(&$item, $name) use ($model) {
                    if (is_array($item) && (!isset($item['options']) || !isset($item['options']['placeholder']) || !$item['options']['placeholder'])) {
                        $item['options']['placeholder'] = \Yii::t('app',isset($item['label'])?$item['label']:$model->getAttributeLabel($name));
                        $item['label']='';
                    }
                });
            }
            unset($options['usePlaceHolders']);
        }
        return $options;
    }

    public static function widget($options = []) {
        $preparedOptions = static::prepareOptions($options);
        return empty($preparedOptions['attributes']) ? '' : parent::widget($preparedOptions);
    }

}
