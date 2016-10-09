<?php

namespace verbi\yii2Helpers\widgets;

use limion\jqueryfileupload\JQueryFileUpload;
/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class FileUpload extends JQueryFileUpload {
    protected $path = '@vendor/limion/yii2-jquery-fileupload-widget/src/views/';
    protected $formStr;
    
    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        if(empty($this->formId)) {
            $this->formId = true;
        }
        parent::init();
        $this->mainView = $this->path . $this->mainView;
        $this->galleryTemplateView = $this->path . $this->galleryTemplateView;
        $this->uploadTemplateView = $this->path . $this->uploadTemplateView;
        $this->downloadTemplateView = $this->path . $this->downloadTemplateView;
        if(!isset($this->clientOptions['dataType'])) {
            $this->clientOptions['dataType'] = 'json';
        }
        if(!isset($this->clientOptions['autoUpload'])) {
            $this->clientOptions['autoUpload'] = false;
        }
    }
    
    public function run() {
        $form = null;
        if($this->formId===true) {
            $form = \verbi\yii2Helpers\widgets\ActiveForm::begin();
            $this->formId = $form->id;
        }
        parent::run();
        if($form) {
            \verbi\yii2Helpers\widgets\ActiveForm::end();
        }
    }
}