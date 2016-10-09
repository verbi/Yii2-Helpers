<?php
namespace verbi\yii2Helpers\behaviors\formatter;
use \verbi\yii2Helpers\behaviors\base\Behavior;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class PhoneFormatterBehavior extends Behavior {
    public function asPhone($value) {
        return $this->owner->asText($value);
    }
}