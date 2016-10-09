<?php
namespace unclead\widgets;

use yii\base\Model;
use yii\db\ActiveRecordInterface;
use unclead\widgets\renderers\TableRenderer;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class MultipleInput extends \unclead\widgets\MultipleInput
{
    const POS_HEADER    = TableRenderer::POS_HEADER;
    const POS_ROW       = TableRenderer::POS_ROW;
    const POS_FOOTER    = TableRenderer::POS_FOOTER;

    /**
     * @var ActiveRecordInterface[]|array[] input data
     */
    public $data = null;

    /**
     * @var array columns configuration
     */
    public $columns = [];

    /**
     * @var integer inputs limit
     */
    public $limit;

    /**
     * @var array client-side attribute options, e.g. enableAjaxValidation. You may use this property in case when
     * you use widget without a model, since in this case widget is not able to detect client-side options
     * automatically.
     */
    public $attributeOptions = [];

    /**
     * @var array the HTML options for the `remove` button
     */
    public $removeButtonOptions;

    /**
     * @var array the HTML options for the `add` button
     */
    public $addButtonOptions;

    /**
     * @var bool whether to allow the empty list
     */
    public $allowEmptyList = false;

    /**
     * @var bool whether to guess column title in case if there is no definition of columns
     */
    public $enableGuessTitle = false;

    /**
     * @var int minimum number of rows
     */
    public $min;

    /**
     * @var string|array position of add button. By default button is rendered in the row.
     */
    public $addButtonPosition = self::POS_ROW;

    /**
     * @var array|\Closure the HTML attributes for the table body rows. This can be either an array
     * specifying the common HTML attributes for all body rows, or an anonymous function that
     * returns an array of the HTML attributes. It should have the following signature:
     *
     * ```php
     * function ($model, $index, $context)
     * ```
     *
     * - `$model`: the current data model being rendered
     * - `$index`: the zero-based index of the data model in the model array
     * - `$context`: the MultipleInput widget object
     *
     */
    public $rowOptions = [];

    /**
     * @var string the name of column class. You can specify your own class to extend base functionality.
     * Defaults to `unclead\widgets\MultipleInputColumn`
     */
    public $columnClass;

    /**
     * Initialization.
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->guessColumns();
        $this->initData();
        parent::init();
    }

    /**
     * Initializes data.
     */
    protected function initData()
    {
        if ($this->data !== null) {
            return;
        }

        if ($this->value !== null) {
            $this->data = $this->value;
            return;
        }

        if ($this->model instanceof Model) {
            foreach ((array)$this->model->{$this->attribute} as $index => $value) {
                $this->data[$index] = $value;
            }
        }
    }

    /**
     * This function tries to guess the columns to show from the given data
     * if [[columns]] are not explicitly specified.
     */
    protected function guessColumns()
    {
        if (empty($this->columns)) {
            $column = [
                'name' => $this->hasModel() ? $this->attribute : $this->name,
                'type' => MultipleInputColumn::TYPE_TEXT_INPUT
            ];

            if ($this->enableGuessTitle && $this->hasModel()) {
                $column['title'] = $this->model->getAttributeLabel($this->attribute);
            }
            $this->columns[] = $column;
        }
    }

    /**
     * Run widget.
     */
    public function run()
    {
        return $this->createRenderer()->render();
    }

    /**
     * @return TableRenderer
     */
    private function createRenderer()
    {
        $config = [
            'id'                => $this->options['id'],
            'columns'           => $this->columns,
            'limit'             => $this->limit,
            'attributeOptions'  => $this->attributeOptions,
            'data'              => $this->data,
            'columnClass'       => $this->columnClass !== null ? $this->columnClass : MultipleInputColumn::className(),
            'allowEmptyList'    => $this->allowEmptyList,
            'min'               => $this->min,
            'addButtonPosition' => $this->addButtonPosition,
            'rowOptions'        => $this->rowOptions,
            'context'           => $this,
        ];

        if (!is_null($this->removeButtonOptions)) {
            $config['removeButtonOptions'] = $this->removeButtonOptions;
        }

        if (!is_null($this->addButtonOptions)) {
            $config['addButtonOptions'] = $this->addButtonOptions;
        }

        return new TableRenderer($config);
    }
}