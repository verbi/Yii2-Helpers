<?php

namespace verbi\yii2Helpers\behaviors\base;

use Yii;
use yii\filters\AccessControl as YiiAccessControl;

class AccessControl extends YiiAccessControl {

    use \verbi\yii2Helpers\traits\BehaviorTrait;

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

}
