<?php

namespace verbi\yii2Helpers\behaviors\base\models;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */

class RelationHandlerBehavior extends \verbi\yii2Helpers\behaviors\base\Behavior {

    use \verbi\yii2ExtendedActiveRecord\traits\ActiveRecordTrait;

    protected $_relations = [];
    protected $_softLinked = [];
    protected $_softDeleted = [];

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
                if (array_key_exists($name, $this->owner->_related)) {
                    return $this->owner->_related[$name];
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
        if (!array_key_exists($name, $this->_relations)) {
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
                        $this->owner->_related[$name] = $models;
                        return;
                    } else {
                        $relationModel = new $relationClassName();
                        $relationModel->setAttributes($value);
                        $this->owner->_related[$name] = $relationModel;
                        return;
                    }
                }
                $this->owner->_related[$name] = $value;
                return;
            }
        }
    }

    public function hasMethod($name, $checkPublic = false) {
        if (parent::hasMethod($name, $checkPublic)) {
            return true;
        }
        if (strpos($name, 'set') === 0 && $this->owner) {
            $relation = $this->_getRelation(strtolower(substr($name, 3)));
            if ($relation) {
                return true;
            }
        }
        return false;
    }

    public function softLink($name, $model, $extraColumns = []) {
        if ($this->owner) {
            $relation = $this->_getRelation($name);
            if ($relation) {
                if (is_array($value)) {
                    $relationClassName = $relation->modelClass;
                    if ($relation->multiple) {
                        if (!isset($this->_softRelated[$name])) {
                            $this->_softLinked[$name] = $this->owner->$name;
                        }
                        foreach ($value as $var) {
                            $relationModel = new $relationClassName();
                            $relationModel->setAttributes($var);
                            $this->_softLinked[$name][] = $relationModel;
                        }
                        return;
                    } else {
                        $relationModel = new $relationClassName();
                        $relationModel->setAttributes($value);
                        $this->_softLinked[$name] = $relationModel;
                        return;
                    }
                }
                $this->_softLinked[$name] = $value;
                return;
            }
        }
    }

    public function softUnlink($name, $model, $delete = false) {
        if ($this->owner) {
            $relation = $this->_getRelation($name);
            
            if ($relation) {
                if ($delete) {
                    $this->softDeleteRelated($name, $model);
                }
                if ($relation->multiple) {
                    $this->_softLinked[$name] = [];
                    return;
                }
                
            }
        }
    }

    public function softUnlinkAll($name, $delete = false) {
        if ($this->owner) {
            $relation = $this->_getRelation($name);
            
            if ($relation) {
                if ($delete) {
                    $this->softDeleteRelated($name);
                }
                $relationClassName = $relation->modelClass;
                if ($relation->multiple) {

                    $this->_softLinked[$name] = [];
                    return;
                }
            }

            $this->_softLinked[$name] = null;
            return;
        }
    }

    public function softDeleteRelated($name, $value = null) {
        if ($this->owner) {
            $relation = $this->_getRelation($name);
            if ($relation) {
                $relationClassName = $relation->modelClass;
                if (!isset($this->_softDeleted[$name])) {
                    $this->_softDeleted[$name] = [];
                }
                if (!$value) {
                    $value = $this->owner->$name;
                }
                if ($relation->multiple) {

                    if (!isset($this->_softRelated[$name])) {
                        if (is_array($value)) {
                            $this->_softDeleted[$name] = array_merge($this->_softDeleted[$name], $value);
                        } else {
                            $this->_softDeleted[$name][] = $value;
                        }
                    } else {
                        if (is_array($value)) {
                            foreach ($value as $item) {
                                if ($item instanceof \yii\db\ActiveRecordInterface && !$item->getIsNewRecord()) {
                                    $this->_softDeleted[$name][] = $item;
                                }
                            }
                        }
                    }
                    $this->_softDeleted[$name] = $this->arrayFilterUniqueActiveRecord($this->_softDeleted[$name]);
                    return;
                }

                if (isset($value) && $value instanceof \yii\db\ActiveRecordInterface && !$value->getIsNewRecord()) {
                    $this->_softDeleted[$name][] = $value;
                    $this->_softDeleted[$name] = $this->arrayFilterUniqueActiveRecord($this->_softDeleted[$name]);
                    return;
                }
            }
        }
    }

}
