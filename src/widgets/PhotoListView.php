<?php
namespace verbi\yii2Helpers\widgets;
use verbi\yii2Helpers\Html;
/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class PhotoListView extends ListView {
    public static function widget($config = [])
    {
        if(!isset($config['itemView'])) {
            $config['itemView'] = function ($model, $key, $index, $widget) {
                return '<div class="col-lg-3 col-md-4 col-xs-6 thumb">
                        <a href="#" class="thumbnail">
                            <img alt="" src="http://placehold.it/400x300" class="img-responsive">
                        </a>
                    </div>';
            };
        }
        return parent::widget($config);
    }
}