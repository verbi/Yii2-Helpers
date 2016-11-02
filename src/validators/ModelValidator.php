<?php
namespace verbi\yii2Helpers\validators;
use Yii;
use yii\base\Model;
use verbi\yii2Helpers\Html;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class ModelValidator extends Validator
{
    /**
     * @var bool Whether the validator should validate a collection of items
     */
    public $multiple = false;
    
    /**
     * @var string the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     */
    public $message;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            if(!$this->getMultiple()) {
                $this->message = Yii::t('yii', '{attribute} is invalid.');
            }
            else {
                $this->message = Yii::t('yii', 'One of the values of {attribute} is invalid.');
            }
        }
    }
    
    /**
     * Checks whether to validate a collection of items
     * @return bool
     */
    public function getMultiple() {
        return $this->multiple;
    }
    
    /**
     * Sets hether to validate a collection of items
     * @param bool $value
     */
    public function setMultiple(bool $value) {
        $this->multiple = $value;
    }
    
    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (!$this->getMultiple() && $value instanceof Model && $value->validate()) {
            return null;
        }
        elseif($this->getMultiple() &&(is_array($value) || $value instanceof \Traversable)) {
            foreach($value as $item) {
                if(!$item instanceof Model || !$item->validate()) {
                    //return [Html::errorSummary($value),[]];
                    return [$this->message, [
                    ]];
                }
            }
            return null;
        }
        return [$this->message, [
            //'requiredValue' => $this->requiredValue,
        ]];
    }
}