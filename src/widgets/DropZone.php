<?php

namespace verbi\yii2Helpers\widgets;

use \verbi\yii2Helpers\Html;
use \yii\helpers\Json;
use \verbi\yii2Helpers\widgets\assets\DropZoneAsset;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class DropZone extends \verbi\yii2Helpers\widgets\Widget
{
    public $options = [];

    public $clientEvents = [];

    public $uploadUrl = ['upload'];
    public $autoDiscover = false;
    

    /**
     * Initializes the widget
     * @throw InvalidConfigException
     */
    public function init()
    {
        parent::init();

        //set defaults
        if (!isset($this->options['url'])) $this->options['url'] = $this->uploadUrl; // Set the url
        $this->options['url'] = \yii\helpers\Url::to($this->options['url']);
        if (!isset($this->options['previewsContainer'])) $this->options['previewsContainer'] = '#' . $this->id.'_preview_container'; // Define the element that should be used as click trigger to select files.
        if (!isset($this->options['clickable'])) $this->options['clickable'] = true; // Define the element that should be used as click trigger to select files.
        $this->autoDiscover = $this->autoDiscover===false?'false':'true';
        
        if(\Yii::$app->getRequest()->enableCsrfValidation){
            $this->options['headers'][\yii\web\Request::CSRF_HEADER] = \Yii::$app->getRequest()->getCsrfToken();
            $this->options['params'][\Yii::$app->getRequest()->csrfParam] = \Yii::$app->getRequest()->getCsrfToken();
        }

        \Yii::setAlias('@dropzone', dirname(__FILE__));
        $this->registerAssets();
    }

    public function run()
    {
        return Html::tag('div', $this->renderDropzone(), ['id' => $this->id, 'class' => 'dropzone']);
    }

    protected function renderDropzone()
    {
        $data = Html::tag('div', '', ['id' => $this->id.'_preview_container','class' => 'dropzone-previews']);

        return $data;
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();

        $js = 'Dropzone.autoDiscover = ' . $this->autoDiscover . '; var ' . $this->id . ' = new Dropzone("div#' . $this->id . '", ' . Json::encode($this->options) . ');';

        if (!empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                $js .= "$this->id.on('$event', $handler);";
            }
        }

        $view->registerJs($js);
        DropZoneAsset::register($view);
    }
}