<?php
namespace verbi\yii2Helpers\behaviors\base\models;
use verbi\yii2ExtendedActiveRecord\db\XActiveRecord;

/* 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class RelationHandlerBehavior extends \verbi\yii2Helpers\behaviors\base\Behavior {
    protected $_relations = [];
    
    protected $relations = [];
    
    public $tags;
    
    public function __get($name) {
        if(isset($this->relations[$name]) || array_key_exists($name, $this->relations)) {
            if(isset($this->_relations[$name])) {
                return $this->_relations[$name];
            }
            return null;
        }
        return parent::__get($name);
    }
    
    public function __set($name, $value) {
        if(isset($this->relations[$name]) || array_key_exists($name, $this->relations)) {
            
            return $this->_relations[$name] = $value;
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
    
    public function events() {
        return [
            XActiveRecord::EVENT_AFTER_SETATTRIBUTES => 'afterSetAttributes',
        ];
    }
    
    public function afterSetAttributes($event) {
        // ...
    }
    
    public function attach($owner)
    {
        $this->owner = $owner;
        foreach ($this->events() as $event => $handler) {
            $owner->on($event, is_string($handler) ? [$this, $handler] : $handler);
        }
    }
    
    public function setRelations($value) {
        $this->relations = $value;
    }
}