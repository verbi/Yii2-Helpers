<?php

namespace verbi\yii2Helpers\widgets;

use limion\jqueryfileupload\JQueryFileUpload;
use yii\helpers\Json;
use verbi\yii2WebController\behaviors\ActionBehavior;
use yii\data\DataProviderInterface;
use yii\data\ArrayDataProvider;
use verbi\yii2Helpers\behaviors\fileUpload\FileUploadModelBehavior;
use verbi\yii2Helpers\widgets\assets\fileUpload\FileUploadPlusUIAsset;
use xsonline\yii2WebController\Controller;
/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class FileUpload extends JQueryFileUpload {
//    protected $path = '@vendor/limion/yii2-jquery-fileupload-widget/src/views/';
    protected $path = '@vendor/verbi/yii2-helpers/src/widgets/views/fileUpload/';
    protected $formStr;
    public $controller;
    public $enablePjax=true;
    public $reloadTime;
    public $clientBinds = [
//        'fileuploadadd' => 'function (e, data) {'
//                . 'if (data.autoUpload || (data.autoUpload !== false &&'
//                    . '$(this).fileupload(\'option\', \'autoUpload\'))) {'
//                        . 'data.process().done(function () {'
//                        . 'data.submit();'
//                    . '});'
//                . '}'
//            . '}',
//        'fileuploaddone' => 'function (e, data) {'
//        // refresh Pjax of files
//        
////        . 'alert("test");'
//        . '}',
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
        if(!isset($this->clientOptions['prependFiles'])) {
            $this->clientOptions['prependFiles'] = true;
        }
        
        if(is_array($this->files)) {
            $this->files = new ArrayDataProvider([
                'allModels' => $this->files,
            ]);
        }
        
        if(isset($this->options['multiple'])
                && $this->options['multiple'] === false) {
            $this->clientOptions['multiple'] = false;
        }
        
        if(!isset($this->controller)) {
            $this->controller = \Yii::$app->controller;
        }
        if(!$this->controller instanceof Controller) {
            if(is_string($this->controller)
                    || is_array($this->controller)
                    || is_callable($this->controller)) {
                $this->controller = \Yii::createObject($this->controller);
            }
            else {
                $this->controller = null;
            }
        }
        if(!$this->controller->getBehaviorByClass(ActionBehavior::className())) {
            $this->controller->attachBehavior(ActionBehavior::className(),ActionBehavior::className());
        }
    }
    
    public function run() {
        $form = null;
        if($this->formId===true) {
            $form = \verbi\yii2Helpers\widgets\ActiveForm::begin();
            $this->formId = $form->id;
        }
        $view = $this->getView();
        FileUploadPlusUIAsset::register($view);
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
                $binds .= '.bind('.Json::htmlEncode($clientkey).', '.$clientBind.')';
            }
        }
        return $binds;
    }
    
    /**
     * Registers required script for the plugin to work as jQuery File Uploader
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        switch ($this->appearance) {
            case 'ui':
                FileUploadAsset::register($view);
                break;    
            default:
        }
        
        return parent::registerClientScript();
    }
    
//    public static function widget($config = [])
//    {
//        $js = '';
//        ob_start();
//        ob_implicit_flush( false );
//
//            $enablePjax = true;
//            if(isset($config['enablePjax'])) {
//                $enablePjax = $config['enablePjax'];
//                unset($config['enablePjax']);
//            }
//            if($enablePjax) {
//                $pjaxConfig = [];
//                if( isset( $config['reloadTime'] ) ) {
//                    $pjaxConfig['reloadTime'] = $config['reloadTime'];
//                    unset( $config['reloadTime'] );
//                }
//                $pjax = Pjax::begin( $pjaxConfig );
//                $config['clientOptions']['done'] = new \yii\web\JsExpression('function (e, data) {'
//                // refresh Pjax of files
//                . '$.pjax.reload({container:"#'.$pjax->id.'"})'
//                . '}');
////                if(isset($config['multiple']) && $config['multiple'] === false) {
//                    $config['clientOptions']['add'] = new \yii\web\JsExpression('function (e, data) {'
//                            . '$(\'#'.$pjax->id.' .files\')'
//                            . '.html(\'\');'
//                            . 'if (data.autoUpload || (data.autoUpload !== false && '
//                                . '$(this).fileupload(\'option\', \'autoUpload\'))) {'
//                                . 'data.process().done(function () {'
//                                    . 'data.submit();'
//                                . '});'
//                            . '}'
//                        . '}');
////                }
//            }
//            echo parent::widget( $config );
//            if($enablePjax) {
//                Pjax::end();
//            }
//        
//        if($js) {
//            $pjax->view->registerJs( $js );
//        }
//        return ob_get_clean();
//    }
}