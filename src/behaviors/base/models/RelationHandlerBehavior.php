<?php

namespace verbi\yii2Helpers\behaviors\base\models;

use verbi\yii2ExtendedActiveRecord\db\ActiveRecord;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */

class RelationHandlerBehavior extends \verbi\yii2Helpers\behaviors\base\Behavior {
    protected $_relations = [];

    public function __call($name, $params) {
        if ($this->owner && strpos($name, 'set') === 0) {
            $relation = $this->_getRelation($name);
            if ($relation) {
                return $this->_setRelated(lcfirst(substr($name, 3)), array_shift($params));
            }
        }
        return parent::__call($name, params);
    }

    public function __get($name) {
        if ($this->owner) {
            $relation = $this->_getRelation($name);
            if ($relation) {
                if (isset($this->owner->_relations[$name])) {
                    return $this->owner->_relations[$name];
                }
            }
        }
        return parent::__get($name);
    }

    public function __set($name, $value) {
        if ($this->owner) {
            $relation = $this->_getRelation($name);
            if ($relation) {
                return $this->_setRelated($name, $value);
            }
        }
        return parent::__set($name, $value);
    }

    public function __isset($name) {
        try {
            return $this->__get($name) !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function _getRelation($name) {
        if (!array_key_exists($this->_relations[$name])) {
            $relation = $this->owner->getRelation($name, false);
            if ($relation instanceof \yii\db\ActiveQueryInterface) {
                $this->_relations[$name] = $relation;
            } else {
                $this->_relations[$name] = null;
            }
            return $this->_relations[$name];
        }
        return $this->_relations[$name];
    }

    protected function _setRelated($name, $value) {
        if ($this->owner) {
            $relation = $this->_getRelation($name);
            if ($relation) {
                if (is_array($value)) {
                    $relationClassName = $relation->modelClass;
                    if ($relation->multiple) {
                        $models = [];
                        foreach ($value as $var) {
                            $relationModel = new $relationClassName();
                            $relationModel->setAttributes($var);
                            $models[] = $relationModel;
                        }
                        $this->owner->_relations[$name] = $models;
                        return;
                    } else {
                        $relationModel = new $relationClassName();
                        $relationModel->setAttributes($value);
                        $this->owner->_relations[$name] = $relationModel;
                        return;
                    }
                }
                $this->owner->_relations[$name] = $value;
                return;
            }
        }
    }
    
    public function hasMethod($name, $checkPublic = false) {
        if(parent::hasMethod($name, $checkPublic)) {
            return true;
        }
         if (strpos($name,'set')===0 && $this->owner) {
            $relation = $this->_getRelation(strtolower(substr($name,3)));
            if ($relation) {
                return true;
            }
        }
        return false;
    }
}
