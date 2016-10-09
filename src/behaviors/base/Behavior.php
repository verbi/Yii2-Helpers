<?php
namespace verbi\yii2Helpers\behaviors\base;

/* 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class Behavior extends \yii\base\Behavior {
    public function behaviors() {
        return array_merge(parent::behaviors(), [
            \verbi\yii2Helpers\behaviors\base\ComponentBehavior::className(),
        ]);
    }
}