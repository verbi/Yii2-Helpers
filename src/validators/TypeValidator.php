<?php
namespace verbi\yii2Helpers\validators;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class TypeValidator extends Validator
{
    public $requiredType;
    /**
     * @var string the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     * - `{requiredType}`: the value of [[requiredType]]
     */
    public $message;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} must be of type "{requiredType}".');
        }
    }
    
    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($this->requiredType === null) {
            return null;
        } elseif (gettype($value) == $this->requiredType || $value instanceof $this->requiredType) {
            return null;
        }
        return [$this->message, [
            'requiredType' => $this->requiredType,
        ]];
    }
}