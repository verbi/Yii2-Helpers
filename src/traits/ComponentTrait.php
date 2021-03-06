<?php

namespace verbi\yii2Helpers\traits;

use yii\base\UnknownPropertyException;
use yii\base\InvalidCallException;
use verbi\yii2Helpers\events\GeneralFunctionEvent;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
trait ComponentTrait {
    static $EVENT_BEFORE_MAGIC_GET = '_beforeMagicGet';
    static $EVENT_BEFORE_MAGIC_SET = '_beforeMagicSet';
    static $EVENT_BEFORE_ATTACH_BEHAVIOR = '_beforeAttachBehavior';
    static $EVENT_AFTER_ATTACH_BEHAVIOR = '_afterAttachBehavior';
    static $EVENT_AFTER_ENSURE_BEHAVIORS = '_afterEnsureBehaviors';
    
    protected $__behaviorsEnsured;
    
    public function __uses() {
        return class_uses(self::ClassName());
    }
    
//    public function init() {
//        parent::init();
//        foreach($this->__uses() as $uses) {
//            $method = '__' . str_replace( '\\', '_', $uses ) . 'Init';
//            if($this->hasMethod($method)) {
//                $this->$method();
//            }
//        }
//    }
    
    /**
     * @inheritdoc
     */
    public function __get($name) {
        $_beforeMagicGet = $this->_beforeMagicGet($name);
        if (is_array($_beforeMagicGet) && $_beforeMagicGet['hasReturnValue']) {
            return $_beforeMagicGet['returnValue'];
        }
        try {
            return parent::__get($name);
        } catch (\Exception $exception) {
            if ($exception instanceof UnknownPropertyException || $exception instanceof InvalidCallException) {
                if ($this->hasMethod('getBehaviors')) {
                    foreach ($this->getBehaviors() as $behavior) {
                        try {
                            $value = $behavior->__get($name);
                            if ($value) {
                                return $value;
                            }
                        } catch (\Exception $e) {
                            if (!$e instanceof UnknownPropertyException && !$e instanceof InvalidCallException) {
                                throw $e;
                            }
                        }
                    }
                }
            }
            throw $exception;
        }
    }

    protected function _beforeMagicGet($name) {
        if ($this->hasMethod('trigger')) {
            $event = new GeneralFunctionEvent([
                'params' => [
                    'name' => $name,
                ],
            ]);
            $this->trigger(self::$EVENT_BEFORE_MAGIC_GET, $event);
            return [
                'hasReturnValue' => $event->hasReturnValue(),
                'returnValue' => $event->getReturnValue(),
            ];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value) {
        $exception = null;
        try {
            parent::__set($name, $value);
        } catch (\Exception $exception) {
            $sw = true;
            if ($exception instanceof UnknownPropertyException || $exception instanceof InvalidCallException) {
                if ($this->hasMethod('getBehaviors')) {
                    foreach ($this->getBehaviors() as $behavior) {
                        try {
                            $behavior->__set($name, $value);
                            $sw = false;
                        } catch (\Exception $e) {
                            if (!$e instanceof UnknownPropertyException && !$e instanceof InvalidCallException) {
                                throw $e;
                            }
                        }
                    }
                }
            }
            if ($sw) {
                throw $exception;
            }
        }
    }

    protected function _beforeMagicSet($name, $value) {
        if ($this->hasMethod('trigger')) {
            $event = new GeneralFunctionEvent([
                'params' => [
                    'name' => $name,
                    'value' => $value,
                ],
            ]);
            $this->trigger(self::$EVENT_BEFORE_MAGIC_SET, $event);
        }
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true) {
        if (method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name) && $this->isPublicProperty($name)) {
            return true;
        } elseif ($checkBehaviors) {
            if ($this->hasMethod('getBehaviors')) {
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
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true) {
        if (method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name) && $this->isPublicProperty($name)) {
            return true;
        } elseif ($checkBehaviors) {
            if ($this->hasMethod('getBehaviors')) {
                foreach ($this->getBehaviors() as $behavior) {
                    if ($behavior->canSetProperty($name, $checkVars)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    public function behaviorsAreEnsured() {
        if($this->__behaviorsEnsured === null) {
            $ref = new \ReflectionProperty(\yii\base\Component::className(), "_behaviors");
            $ref->setAccessible(true);
            $this->__behaviorsEnsured = ($ref->getValue($this) !== null);
        }
        return $this->__behaviorsEnsured;
    }
    
    /**
     * @inheritdoc
     */
    public function ensureBehaviors()
    {
        if (!$this->behaviorsAreEnsured()) {
            parent::ensureBehaviors();
            $this->__behaviorsEnsured = true;
            $event = new GeneralFunctionEvent();
            $this->trigger(static::$EVENT_AFTER_ENSURE_BEHAVIORS, $event);
        }
    }

    public function isPublicProperty($name) {
        $reflect = new \ReflectionClass($this);
        return $reflect->getProperty($name)->isPublic();
    }

    public function getMethods() {
        $methods = get_class_methods($this);
        if (method_exists($this, 'getBehaviors')) {
            foreach ($this->getBehaviors() as $behavior) {
                if ($behavior->hasMethod('getMethods')) {
                    $methods = array_merge($behavior->getMethods(), $methods);
                } else {
                    $methods = array_merge(get_class_methods($behavior), $methods);
                }
            }
        }
        return $methods;
    }

    public function hasMethod($name, $checkPublic = false) {
        if (parent::hasMethod($name)) {
            if (!$checkPublic || $this->isPublicMethod($name)) {
                return true;
            }
            return false;
        }
        if (method_exists($this, 'getBehaviors')) {
            foreach ($this->getBehaviors() as $behavior) {
                if ($behavior->hasMethod($name, true)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isPublicMethod($name) {
        $reflect = new \ReflectionClass($this);
        return $reflect->getMethod($name)->isPublic();
    }

    public function getReflectionMethod($name, $checkBehaviors = true) {
        if (method_exists($this, $name)) {
            return new \ReflectionMethod($this, $name);
        } elseif ($checkBehaviors) {
            $this->ensureBehaviors();
            foreach ($this->getBehaviors() as $behavior) {
                if ($behavior->hasMethod($name)) {
                    if ($behavior->hasMethod('getReflectionMethod')) {
                        return $behavior->getReflectionMethod($name, $checkBehaviors);
                    } else {
                        return new \ReflectionMethod($behavior, $name);
                    }
                }
            }
        }
        return null;
    }

    public function getBehaviorsByClass($className) {
        return array_filter(
                $this->getBehaviors(), function ($e) use (&$className) {
                return $e instanceof $className;
            }
        );
    }
    
    public function getBehaviorByClass($className) {
        return $this->getBehaviorsByClass($className);
    }

    public function hasBehaviorByClass($className) {
        return $this->getBehaviorByClass($className) ? true : false;
    }
    
    public function attachBehavior($name, $behavior)
    {
        if ($this->hasMethod('trigger')) {
            $event = new GeneralFunctionEvent([
                'params' => [
                    'name' => $name,
                    'behavior' => $behavior,
                ],
            ]);
            $this->trigger(static::$EVENT_BEFORE_ATTACH_BEHAVIOR, $event);
            if(!$event->isValid) {
                return false;
            }
        }
        $result = parent::attachBehavior($name, $behavior);
        if ($this->hasMethod('trigger')) {
            $this->trigger(static::$EVENT_AFTER_ATTACH_BEHAVIOR, $event);
        }
        return $result;
    }
    
    public function getComponentId($app = null) {
        if($app===null) {
            $app = \Yii::$app;
        }
        foreach($app->getComponents(false) as $key => $component) {
            if($this===$component) {
                return $key;
            }
        }
        return null;
    }
    
    public static function className(bool $short = false) {
        $className = parent::className();
        if($short) {
            $explodedClassName = explode('\\',$className);
            $className = end($explodedClassName);
        }
        return $className;
    }
}
