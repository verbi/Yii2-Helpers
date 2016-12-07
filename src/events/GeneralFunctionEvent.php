<?php
namespace verbi\yii2Helpers\events;

class GeneralFunctionEvent extends \yii\base\Event {
    protected $returnValue;
    protected $hasReturnValue = false;
    protected $params;
    public $isValid = true;
    
    public function setReturnValue($value) {
        $this->returnValue = $value;
        $this->hasReturnValue = true;
    }
    
    public function getParams() {
        return $this->params;
    }
    
    public function setParams($params) {
        $this->params = $params;
    }
    
    public function getReturnValue() {
        return $this->returnValue;
    }
    
    public function hasReturnValue() {
        return $this->hasReturnValue;
    }
}