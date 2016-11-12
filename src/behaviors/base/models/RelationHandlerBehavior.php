<?php

namespace verbi\yii2Helpers\behaviors\base\models;

use yii\db\ActiveRecordInterface;
use verbi\yii2Helpers\events\GeneralFunctionEvent;
use yii\db\ActiveQueryInterface;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class RelationHandlerBehavior extends \verbi\yii2Helpers\behaviors\base\Behavior {
    use \verbi\yii2ExtendedActiveRecord\traits\ActiveRecordTrait;

    protected $_relations = [];
    protected $_related = [];

    public function events() {
        $ownerClass = $this->owner->className();
        return [
            $ownerClass::$EVENT_BEFORE_MAGIC_GET => 'beforeMagicGet',
            $ownerClass::$EVENT_BEFORE_MAGIC_SET => 'beforeMagicSet',
        ];
    }

    public function __call($name, $params) {
        if ($this->owner && strpos($name, 'set') === 0) {
            $relation = $this->_getRelation(lcfirst(substr($name, 2)));
            if ($relation) {
                return $this->_setRelated(lcfirst(substr($name, 3)), array_shift($params));
            }
        }
        return parent::__call($name, $params);
    }

    public function __get($name) {
        if ($this->owner) {
            $relation = $this->_getRelation($name);
            if ($relation) {
                if (array_key_exists($name, $this->_related)) {
                    return $this->_related[$name];
                }
            }
        }
        return parent::__get($name);
    }

    public function beforeMagicGet(GeneralFunctionEvent $event) {
        try {
            $event->setReturnValue($this->__get($event->params['name']));
            $event->handled = true;
        } catch (\Exception $e) {
            
        }
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

    protected function beforeMagicSet(GeneralFunctionEvent $event) {
        try {
            $this->__set($event->params['name'], $event->params['value']);
            $event->handled = true;
        } catch (\Exception $e) {
            
        }
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
            if ($relation instanceof ActiveQueryInterface) {
                if (is_array($value)) {
                    $relationClassName = $relation->modelClass;
                    if($relation->via === null) {
                        $viaRelation = $relation;
                    }
                    elseif (is_array($relation->via)) {
                        /* @var $viaRelation ActiveQuery */
                        list($viaName, $viaRelation) = $relation->via;
                        $viaClass = $viaRelation->modelClass;
                        // unset $viaName so that it can be reloaded to reflect the change
                        unset($this->_related[$viaName]);
                    } else {
                        $viaRelation = $relation->via;
                        $viaTable = reset($relation->via->from);
                    }
                    if ($relation->multiple) {
                        $models = $value;
                        array_walk($models, function(&$var) use ($relationClassName, $viaRelation) {
                            if(is_array($var)) {
                                $relationModel = new $relationClassName();
                                $relationModel->setAttributes($var);
                                array_walk($viaRelation->link, function($primaryKey, $relationKey) use ($relationModel) {
                                    $relationModel->$relationKey = $this->owner->$primaryKey;
                                });
                                $var = $relationModel;
                            }
                        });
                        $this->_related[$name] = $models;
                        return;
                    } else {
                        $relationModel = new $relationClassName();
                        $relationModel->setAttributes($value);
                        array_walk($viaRelation->link, function($primaryKey, $relationKey) use ($relationModel) {
                            $relationModel->$relationKey = $this->owner->$primaryKey;
                        });
                        $this->_related[$name] = $relationModel;
                        return;
                    }
                }
                $this->_related[$name] = $value;
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

    protected function _cleanupSoftDeleteRelated($name, $value = null) {
        if ($value == null) {
            $value = $this->owner->$name;
        }
        if (isset($this->_softDeleted[$name]) && is_array($this->_softDeleted[$name])) {
            $this->_softDeleted[$name] = array_filter($this->_softDeleted[$name], function($obj) use ($value) {
                if (is_array($value)) {
                    foreach ($value as $model) {
                        if ($model instanceof ActiveRecordInterface
                                && $obj instanceof ActiveRecordInterface
                                && ($obj == $model || (!$model->getIsNewRecord()
                                        && !$obj->getIsNewRecord()
                                        && $obj->getPrimaryKey(true) == $model->getPrimaryKey(true)
                                ) )) {
                            return false;
                        }
                    }
                }
                if ($value instanceof ActiveRecordInterface && $obj instanceof ActiveRecordInterface && ($obj == $model || (!$value->getIsNewRecord() && !$obj->getIsNewRecord() && $obj->getPrimaryKey(true) == $value->getPrimaryKey(true)
                        ) )) {
                    return false;
                }
                return true;
            });
        }
    }

}
