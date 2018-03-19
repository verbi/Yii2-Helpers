<?php

namespace verbi\yii2Helpers\widgets;

use yii\db\BaseActiveRecord;
use verbi\yii2Helpers\widgets\multipleInput\renderers\TableRenderer;

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
    
    /**
     * Run widget.
     */
    public function run()
    {
        $this->rendererClass = $this->rendererClass ?$this->rendererClass: TableRenderer::className();
        return parent::run();
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

    /**
     * @return TableRenderer
     */
//    private function createRenderer()
//    {
//        $this->rendererClass = $this->rendererClass ?$this->rendererClass: TableRenderer::className();
//        
//        
//        $config = [
//            'id'                => $this->options['id'],
//            'columns'           => $this->columns,
//            'limit'             => $this->limit,
//            'attributeOptions'  => $this->attributeOptions,
//            'data'              => $this->data,
//            'columnClass'       => $this->columnClass !== null ? $this->columnClass : MultipleInputColumn::className(),
//            'allowEmptyList'    => $this->allowEmptyList,
//            'min'               => $this->min,
//            'addButtonPosition' => $this->addButtonPosition,
//            'rowOptions'        => $this->rowOptions,
//            'context'           => $this,
//        ];
//
//        if ($this->removeButtonOptions !== null) {
//            $config['removeButtonOptions'] = $this->removeButtonOptions;
//        }
//
//        if ($this->addButtonOptions !== null) {
//            $config['addButtonOptions'] = $this->addButtonOptions;
//        }
//
//        $config['class'] = $this->rendererClass ?: TableRenderer::className();
//
//        return Yii::createObject($config);
//    }
}
