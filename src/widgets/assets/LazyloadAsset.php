<?php

namespace verbi\yii2Helpers\widgets\assets;
use yii\web\AssetBundle;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class LazyloadAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery.lazyload';
    public $js = [
        'jquery.lazyload.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}