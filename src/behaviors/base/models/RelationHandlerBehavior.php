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
            if($relation) {
                if(isset($this->_relations[$name])) {
                    return $this->_relations[$name];
                }
                return null;
            }
        }
        return parent::__get($name);
    }
    
    public function __set($name, $value) {
        if($this->owner)
        {
            $relation = $this->owner->getRelation($name, false);
            if($relation instanceof \yii\db\ActiveQueryInterface) {
                if(is_array($value)) {
                    $relationClassName = $relation->modelClass;
                    if($relation->multiple) {
                        $this->_relations[$name] = array_map(
                            function(&$var) {
                                if(is_array($var)){
                                    $relationModel = new $relationClassName();
                                    $relationModel->setAttributes($var);
                                    return $relationModel;
                                }
                                return $var;
                            },
                            $value
                        );
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