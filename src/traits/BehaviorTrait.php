<?php
namespace verbi\yii2Helpers\traits;
use verbi\yii2ExtendedActiveRecord\base\ModelEvent;
trait BehaviorTrait {
    public static $EVENT_ON_ATTACH = 'onAttach';
    
    public function attach($owner) {
        parent::attach($owner);
        $event = new ModelEvent();
        $this->trigger(static::$EVENT_ON_ATTACH, $event);
    }
    
    public function trigger($name, $event) {
        //return $this->owner->trigger($name, $event);
    }
}