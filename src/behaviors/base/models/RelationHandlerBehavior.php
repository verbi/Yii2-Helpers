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
    
    public function __get($name) {
        if($this->owner) {
            $relation = $this->owner->getRelation($name, false);
            if($relation instanceof \yii\db\ActiveQueryInterface) {
                if(isset($this->_relations[$name])) {
                    return $this->_relations[$name];
                }
                if($relation->multiple) {
                    return $relation->all();
                }
                return $relation->one();
            }
        }
        return parent::__get($name);
    }
    
    public function __set($name, $value) {
        if($this->owner) {
            $relation = $this->owner->getRelation($name, false);
            if($relation instanceof \yii\db\ActiveQueryInterface) {
                if(is_array($value)) {
                    $relationClassName = $relation->modelClass;
                    if($relation->multiple) {
                        $models = [];
                        foreach($value as $var) {
                            $relationModel = new $relationClassName();
                            $relationModel->setAttributes($var);
                            $models[] = $relationModel;
                        }
                        $this->_relations[$name] = $models;
                        return;
                    }
                    else {
                        $relationModel = new $relationClassName();
                        $relationModel->setAttributes($value);
                        $this->_relations[$name] = $relationModel;
                        return;
                    }
                }
                $this->_relations[$name] = $value;
                return;
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
}