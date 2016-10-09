<?php

namespace unclead\widgets;

use unclead\widgets\components\BaseColumn;
use yii\base\Model;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class TabularColumn extends BaseColumn
{
    /**
     * Returns element's name.
     *
     * @param int|null $index current row index
     * @param bool $withPrefix whether to add prefix.
     * @return string
     */
    public function getElementName($index, $withPrefix = true)
    {
        if (is_null($index)) {
            $index = '{' . $this->renderer->getIndexPlaceholder() . '}';
        }

        $elementName = '[' . $index . '][' . $this->name . ']';
        $prefix = $withPrefix ? $this->getModel()->formName() : '';

        return  $prefix . $elementName;
    }

    /**
     * Returns first error of the current model.
     *
     * @param $index
     * @return string
     */
    public function getFirstError($index)
    {
        return $this->getModel()->getFirstError($this->name);
    }

    /**
     * Ensure that model is an instance of yii\base\Model.
     *
     * @param $model
     * @return bool
     */
    protected function ensureModel($model)
    {
        return $model instanceof Model;
    }

    /**
     * @inheritdoc
     */
    public function setModel($model)
    {
        $currentModel = $this->getModel();

        // If model is null and current model is not empty it means that widget renders a template
        // In this case we have to unset all model attributes
        if ($model === null && $currentModel !== null) {
            foreach ($currentModel->attributes() as $attribute) {
                $currentModel->$attribute = null;
            }
        } else {
            parent::setModel($model);
        }
    }
}