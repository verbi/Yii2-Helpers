<?php

/**
 * @link https://github.com/unclead/yii2-multiple-input
 * @copyright Copyright (c) 2014 unclead
 * @license https://github.com/unclead/yii2-multiple-input/blob/master/LICENSE.md
 */

namespace verbi\yii2Helpers\widgets\assets;

use yii\web\AssetBundle;

/**
 * Class MultipleInputAsset
 * @package unclead\widgets\assets
 */
class MultipleInputAsset extends AssetBundle
{
    public $css = [
        'css/multiple-input.css'
    ];

    public $js = [];

    public $depends = [
        'unclead\widgets\assets\MultipleInputAsset',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/multipleInputAssets/';
        $this->js = [
            'js/jquery.multipleInput.js',
        ];
        parent::init();
    }


} 