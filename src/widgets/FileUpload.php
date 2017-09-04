<?php

namespace verbi\yii2Helpers\widgets;

use limion\jqueryfileupload\JQueryFileUpload;
use yii\helpers\Json;
use yii\data\DataProviderInterface;
use yii\data\ArrayDataProvider;
use verbi\yii2Helpers\behaviors\fileUpload\FileUploadModelBehavior;
/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class FileUpload extends JQueryFileUpload {
//    protected $path = '@vendor/limion/yii2-jquery-fileupload-widget/src/views/';
    protected $path = '@vendor/verbi/yii2-helpers/src/widgets/views/fileUpload/';
    protected $formStr;
    public $clientBinds = [
//        'fileuploadadd' => 'function (e, data) {'
//                . 'if (data.autoUpload || (data.autoUpload !== false &&'
//                    . '$(this).fileupload(\'option\', \'autoUpload\'))) {'
//                        . 'data.process().done(function () {'
//                        . 'data.submit();'
//                    . '});'
//                . '}'
//            . '}',
        'fileuploaddone' => 'function (e, data) {'
        // refresh Pjax of files
        . ''
        . '})',
    ];
    public $files = [];
//    public $options = ['multiple'=>false,];
    
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
        
        if(is_array($this->files)) {
            $this->files = new ArrayDataProvider([
                'allModels' => $this->files,
            ]);
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
    
    protected function registerClientOptions($name, $id)
    {
        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "jQuery('#$id').$name($options)".$this->generateClientBinds().";";
            $this->getView()->registerJs($js);
        }
    }
    
    protected function generateClientBinds() {
        $binds = '';
        if(!empty($this->clientBinds)) {
            foreach($this->clientBinds as $clientkey => $clientBind) {
                $binds .= '.bind('.Json::htmlEncode($clientkey).'\', '.$clientBind.')';
            }
        }
        return $binds;
    }
}