<?php

namespace verbi\yii2Helpers\widgets;

use yii\db\BaseActiveRecord;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */

class MultipleInput extends \unclead\widgets\MultipleInput {

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        parent::init();
        if (isset($this->columns) && $this->model instanceof BaseActiveRecord) {
            $columns = [];
            foreach ($this->model->getPrimaryKey(true) as $key => $value) {
                if (false === array_search($key, array_column($this->columns, 'name'))) {
                    $columns[] = [
                        'name' => $key,
                        'type' => 'hiddenInput',
                    ];
                }
            }
            $this->columns = array_merge_recursive($columns, $this->columns);
        }
    }

    public static function widget($options = []) {
        return parent::widget(
                        array_merge([
                    'limit' => 6,
                    'allowEmptyList' => true,
                    'enableGuessTitle' => true,
                    'min' => 0,
                    'addButtonPosition' => MultipleInput::POS_HEADER // show add button in the header
                                ], $options)
        );
    }

}
