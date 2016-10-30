<?php
namespace verbi\yii2Helpers\traits;
use \yii\web\BadRequestHttpException;


/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
trait ComponentTrait {
    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        }
        catch(\Exception $exception) {
            if($this->hasMethod('getBehaviors')) {
                foreach($this->getBehaviors() as $behavior) {
                    try {
                        $value = $behavior->__get($name);
                        if($value) {
                            return $value;
                        }
                    }
                    catch(\Exception $e) {

                    }
                }
            }
            throw $exception;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        $exception = null;
        try {
            parent::__set($name, $value);
        } catch (\Exception $exception) {
            $sw = true;
            if($this->hasMethod('getBehaviors')) {
                foreach($this->getBehaviors() as $behavior) {
                    try {
                        $behavior->__set($name, $value);
                        $sw = false;
                    }
                    catch(\Exception $e) {

                    }
                }
            }
            if($sw) { 
                throw $exception;
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name) && $this->isPublicProperty($name)) {
            return true;
        } elseif ($checkBehaviors) {
            if($this->hasMethod('getBehaviors')) {
                foreach ($this->getBehaviors() as $behavior) {
                    if ($behavior->canGetProperty($name, $checkVars)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name) && $this->isPublicProperty($name)) {
            return true;
        } elseif ($checkBehaviors) {
            if($this->hasMethod('getBehaviors')) {
                foreach ($this->getBehaviors() as $behavior) {
                    if ($behavior->canSetProperty($name, $checkVars)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    public function isPublicProperty($name) {
        $reflect = new \ReflectionClass($this);
        return $reflect->getProperty($name)->isPublic();
    }
    
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