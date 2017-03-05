<?php
namespace verbi\yii2Helpers\behaviors\base;

/* 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class Behavior extends \yii\base\Behavior {
    use \verbi\yii2Helpers\traits\ComponentTrait;
    use \verbi\yii2Helpers\traits\BehaviorTrait;
    
    public function attach($owner)
    {
        $this->owner = $owner;
        foreach ($this->events() as $event => $handler) {
            $owner->on($event, is_string($handler) ? [$this, $handler] : $handler);
        }
    }
}