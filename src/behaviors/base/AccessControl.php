<?php

namespace verbi\yii2Helpers\behaviors\base;

use Yii;
use yii\filters\AccessControl as YiiAccessControl;
use verbi\yii2Helpers\events\GeneralFunctionEvent;
use verbi\yii2Helpers\traits\BehaviorTrait;
use verbi\yii2Helpers\traits\ComponentTrait;


class AccessControl extends YiiAccessControl {
    use \verbi\yii2Helpers\traits\BehaviorTrait {
        events as traitEvents;
        attach as traitAttach;
    }
    
    const EVENT_BEFORE_GENERATE_RULES = 'before_generate_rules';
    const EVENT_AFTER_GENERATE_RULES = 'after_generate_rules';

    public $ruleConfig = ['class' => 'verbi\yii2Helpers\behaviors\base\filters\AccessRule'];
    
    protected $_request;
    public $generateRules;
    
    
    public function events() {
        return array_merge(
            $this->traitEvents(),
            [
                BehaviorTrait::$EVENT_ON_ATTACH => 'afterAttach',
            ]
        );
    }
    
    public function attach($owner) {
        $this->traitAttach($owner);
        if ($this->generateRules !== false && !sizeof($this->rules)) {
            $this->generateRules = true;
            $this->rules = $this->generateRules();
        }
    }
    
    protected function _isEnsured() {
        if(!$this->owner instanceof ComponentTrait) {
            return $this->owner->behaviorsAreEnsured();
        }
        return false;
    }
    
    public function afterAttach($event) {
        if($this->generateRules === true && $this->_isEnsured()) {
            $behavior = $event->data['behavior'];
            if($behavior && $behavior !== $this) {
                $this->rules = $this->getRulesFromBehavior($behavior, $this->rules);
            }
        }
    }
    
    public function afterEnsureBehaviors($event) {
        if($this->_isEnsured()) {
            $this->generateRules();
        }
    }

    protected function generateRules() {
        $event = new GeneralFunctionEvent;
        $this->owner->trigger(self::EVENT_BEFORE_GENERATE_RULES, $event);
        if (!$event->isValid) {
            return $event->getReturnValue() === null ? [] : $event->getReturnValue();
        }
        $rules = [];
        if ($this->owner->hasMethod('getActions')) {
            $actionIds = array_keys($this->owner->getActions());
            foreach ($actionIds as $id) {
                $rules[$id] = $this->generateRule($id);
            }
        }
//        if($this->owner->hasMethod('loadModel')) {
//            foreach($this->owner->loadModel()->getBehaviors() as $behavior) {
//                $rules = $this->getRulesFromBehavior($behavior, $rules);
//            }
//        }
        
        $event = new GeneralFunctionEvent;
        $event->setParams([
            'rules' => &$rules,
        ]);
        $this->owner->trigger(self::EVENT_AFTER_GENERATE_RULES, $event);
        if ($event->isValid && $event->hasReturnValue()) {
            return $event->getReturnValue();
        }
        return $rules;
    }
    
    protected function getRulesFromBehavior($behavior, $rules) {
        if($behavior->hasMethod('addAuthRules')) {
            $behavior->addAuthRules($this->owner);
        }
        if($behavior->hasMethod('getAccessRules')) {
            $rules = array_merge($rules,$behavior->getAccessRules($this));
        }
        return $rules;
    }

    protected function generateRule($actionId) {
        return Yii::createObject(array_merge($this->ruleConfig, [
                    'allow' => true,
                    'actions' => [$actionId],
                    'roles' => [$this->owner->className() . '-' . $actionId],
                    'roleParams' => function() {
                        return ['model' => $this->owner->loadModel($this->owner->getPkFromRequest())];
                    }
        ]));
    }
    
    protected function getRequest() {
        
        if($this->_request === null)
        {
            $this->_request = clone Yii::$app->getRequest();
        }
        return $this->_request;
    }

    public function checkAccess($action, $params, $method = 'get')
    {
        $user = $this->user;
        $request = clone Yii::$app->getRequest();
        $request->setQueryParams ( $params );
        /* @var $rule AccessRule */
        foreach ($this->rules as $rule) {
            if ($rule->allows($action, $user, $request)) {
                return true;
            }
        }
        return false;
    }
}