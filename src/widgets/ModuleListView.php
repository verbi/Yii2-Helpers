<?php
namespace verbi\yii2Helpers\widgets;
/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class ModuleListView extends ListView {
    public static function widget($config = [])
    {
        if(!isset($config['itemView'])) {
            $config['itemView'] = '@vendor/verbi/yii2-helpers/src/widgets/views/panelList/index.php';
        }
        return parent::widget($config);
    }
}