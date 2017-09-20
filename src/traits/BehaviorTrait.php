<?php
namespace verbi\yii2Helpers\traits;
use yii\base\Event;
use verbi\yii2ExtendedActiveRecord\base\ModelEvent;
use yii\base\Component;
trait BehaviorTrait {
    public static $EVENT_ON_ATTACH = 'eventOnAttach';
    
    public $events = [];
    
    public function attach($owner) {
        parent::attach($owner);
        foreach ($this->events() as $event => $handler) {
            $owner->on($event, is_string($handler) ? [$this, $handler] : $handler);
        }
        if($owner instanceof Component) {
            $event = new Event([
                'data' => [
                    'owner' => $owner,
                    'behavior' => $this,
                ],
            ]);
            $owner->trigger(static::$EVENT_ON_ATTACH, $event);
        }
    }
    
    public function events() {
        return $this->events;
    }
    
    public function trigger($name, $event = null) {
//        return $this->owner->trigger($name, $event);
    }
}