<?php

/**
 * @link https://github.com/unclead/yii2-multiple-input
 * @copyright Copyright (c) 2014 unclead
 * @license https://github.com/unclead/yii2-multiple-input/blob/master/LICENSE.md
 */

namespace verbi\yii2Helpers\widgets\multipleInput\renderers;

use verbi\yii2Helpers\widgets\assets\MultipleInputAsset;

/**
 * Class TableRenderer
 * @package unclead\widgets\renderers
 */
class TableRenderer extends \unclead\widgets\renderers\TableRenderer
{
    /**
     * Register script.
     *
     * @throws \yii\base\InvalidParamException
     */
    protected function registerAssets()
    {
        $view = $this->context->getView();
        MultipleInputAsset::register($view);
        parent::registerAssets(); 
    }
    
    
    
//    protected function registerAssets()
//    {
//        $view = $this->context->getView();
//        MultipleInputAsset::register($view);
//        
//        $jsBefore = $this->collectJsTemplates();
//        $template = $this->prepareTemplate();
//        $jsTemplates = $this->collectJsTemplates($jsBefore);
//
//        $options = Json::encode([
//            'id'                => $this->id,
//            'template'          => $template,
//            'jsTemplates'       => $jsTemplates,
//            'limit'             => $this->limit,
//            'min'               => $this->min,
//            'attributeOptions'  => $this->attributeOptions,
//            'indexPlaceholder'  => $this->getIndexPlaceholder()
//        ]);
//
//        $js = "jQuery('#{$this->id}').multipleInput($options);";
//        $view->registerJs($js);
//    }
}
