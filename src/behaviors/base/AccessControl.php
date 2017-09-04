<?php

namespace verbi\yii2Helpers\behaviors\base;

use Yii;
use yii\filters\AccessControl as YiiAccessControl;

class AccessControl extends YiiAccessControl {

    use \verbi\yii2Helpers\traits\BehaviorTrait;

    protected $_request;
    
    public function attach($owner) {
        parent::attach($owner);
        if (!sizeof($this->rules)) {
            $this->rules = $this->generateRules();
        }
    }

    protected function generateRules() {
        $rules = [];
        if ($this->owner->hasMethod('getActions')) {
            $actionIds = array_keys($this->owner->getActions());
            foreach ($actionIds as $id) {
                $rules[$id] = $this->generateRule($id);
            }
        }
        if($this->owner->hasMethod('loadModel')) {
            foreach($this->owner->loadModel()->getBehaviors() as $behavior) {
                if($behavior->hasMethod('addAuthRules')) {
                    $behavior->addAuthRules($this->owner);
                }
            }
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
//        $request->method = $method;
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
