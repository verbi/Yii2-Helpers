<?php
namespace verbi\yii2Helpers\behaviors\base;

use Yii;
use yii\filters\AccessControl as YiiAccessControl;

class AccessControl extends YiiAccessControl {
    public function attach($owner) {
        parent::attach($owner);
        if(!sizeof($this->rules)) {
            $this->rules = $this->generateRules();
        }
    }
    
    
    protected function generateRules() {
        $rules = [];
        if($this->owner->hasMethod('getActions')) {
            $actionIds = array_keys($this->owner->getActions());
            foreach($actionIds as $id) {
                $rules[$id] = Yii::createObject(array_merge($this->ruleConfig, [
                    'allow' => true,
                    'actions' => [$id],
                    'roles' => [$this->owner->className() . '-' . $id],
                ]));
            }
        }
        return $rules;
    }
}