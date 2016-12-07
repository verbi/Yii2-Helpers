<?php
namespace verbi\yii2Helpers\base;

class ArrayObject extends \ArrayObject {
    public function prepend($value) {
        $array = (array)$this;
        array_unshift($array, $value);
        $this->exchangeArray($array);
    }
}