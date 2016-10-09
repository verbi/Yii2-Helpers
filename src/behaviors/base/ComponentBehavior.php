<?php
namespace verbi\yii2Helpers\behaviors\base;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class ComponentBehavior extends \yii\base\Behavior {
    public function getMethods() {
        $methods = get_class_methods($this->owner);
        
        foreach ($this->owner->getBehaviors() as $behavior) {
            if($behavior!==$this && $behavior->hasMethod('getMethods')) {
                $methods = array_merge($behavior->getMethods(),$methods);
            }
            else {
                $methods = array_merge(get_class_methods($behavior),$methods);
            }
        }
        return $methods;
    }
    
    public function getReflectionMethod($name, $checkBehaviors = true) {
        if (method_exists($this->owner, $name)) {
            return new \ReflectionMethod($this->owner, $name);
        } elseif ($checkBehaviors) {
            $this->owner->ensureBehaviors();
            foreach ($this->owner->getBehaviors() as $behavior) {
                if ($behavior->hasMethod($name) ) {
                    if($behavior->hasMethod('getReflectionMethod')) {
                        return $behavior->getReflectionMethod($name, $checkBehaviors);
                    }
                    else {
                        return new \ReflectionMethod($behavior, $name);
                    }
                }
            }
        }
        return null;
    }
    
    public function getBehaviorByClass($className) {
        return array_filter(
                $this->owner->getBehaviors(),
                function ($e) use (&$className) {
                    return $e instanceof $className;
                }
                );
    }
    
    public function hasBehaviorByClass($className) {
        return $this->owner->getBehaviorByClass($className)?true:false;
    }
}