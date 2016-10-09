<?php
namespace verbi\yii2Helpers\validators;
use Yii;
use yii\base\Model;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class ModelValidator extends Validator
{
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
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }
    
    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($value instanceof Model && $value->validate()) {
            return null;
        }
        return [$this->message, [
            'requiredValue' => $this->requiredValue,
        ]];
    }
}