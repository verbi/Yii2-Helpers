<?php

namespace verbi\yii2Helpers\widgets;
/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class MultipleInput extends \unclead\widgets\MultipleInput {
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        parent::init();
    }
    
    public static function widget($options = []) {
        $options = array_merge([
            'limit' => 6,
            'allowEmptyList' => false,
            'enableGuessTitle' => true,
            'min' => 1, // should be at least 2 rows
            'addButtonPosition' => MultipleInput::POS_HEADER // show add button in the header
                ], $options);
        return parent::widget($options);
    }

}
