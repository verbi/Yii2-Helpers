<?php
namespace verbi\yii2Helpers\widgets;
use yii\widgets\Spaceless;
use yii\helpers\Json;
use verbi\yii2Helpers\widgets\assets\PjaxAsset;
use yii\web\JsExpression;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class Pjax extends \yii\widgets\Pjax {
    protected $_id = null;
    
    public static $counter=[];
    
    public $clientOptions = [
        'skipOuterContainers'=>true,
    ];
    
    protected $_reloadTime;
    
    public function getId($autoGenerate = true)
    {
        if ($autoGenerate && $this->_id === null) {
            $uniqueId = str_replace('/','-',\Yii::$app->controller->action->uniqueId);
            if(!isset(static::$counter[$uniqueId])) {
                static::$counter[$uniqueId] = 0;
            }
            $this->_id = static::$autoIdPrefix . '-pjax-' . $uniqueId . '-' . static::$counter[$uniqueId]++;
        }
        return $this->_id;
    }
    
    public function setId($value)
    {
        $this->_id = $value;
    }
    
    public static function begin($config=[]) {
        $reloadTime=null;
        if(isset($config['reloadTime'])) {
            $reloadTime=$config['reloadTime'];
            unset($config['reloadTime']);
        }
        $return =  parent::begin($config);
        if($reloadTime) {
            $return->_reloadTime = $reloadTime;
        }
        Spaceless::begin(); 
        return $return;
    }
    
    public static function end() {
        $js = '';
        Spaceless::end();
        $return = parent::end();
        if($return->_reloadTime) {
            $js .= 'var oauth2 = new Oauth2(' . json_encode(['siteBaseUrl' => \Yii::$app->homeUrl,]) . ');'
                    . 'setInterval(function(){ $.pjax.reload('
                    . '{container: "#'
                    . $return->id
                    . '", async:true'
                    . ((!\yii::$app->user->isGuest) 
                    ? ',headers: {'
                        . '"Authorization":"Bearer " + oauth2.getAjaxAccessToken()'
                    . '},'
                    : '')
                    . '});},'
                    . ($return->_reloadTime*1000)
                    . ');';
        }
        $return->view->registerJs($js);
        return $return;
    }
    
    public function registerClientScript()
    {
        if(!\yii::$app->user->isGuest) {
            if(!isset($this->clientOptions['headers'])) {
                $this->clientOptions['headers'] = [];
            }
            $this->clientOptions['headers']['Authorization'] = new JsExpression(' ('
                    . '"Bearer " + (new Oauth2(' . json_encode(['siteBaseUrl' => \Yii::$app->homeUrl,]) . ')).getAjaxAccessToken()'
                    . ')');
        }
        $id = $this->options['id'];
        $this->clientOptions['headers']['X-PJAX'] = 'true';
        $this->clientOptions['headers']['X-PJAX-Container'] = '#'.$id;
        
        
        $this->clientOptions['push'] = $this->enablePushState;
        $this->clientOptions['replace'] = $this->enableReplaceState;
        $this->clientOptions['timeout'] = $this->timeout;
        $this->clientOptions['scrollTo'] = $this->scrollTo;
        $options = Json::htmlEncode($this->clientOptions);

        $js = '';
        if ($this->linkSelector !== false) {
            $linkSelector = Json::htmlEncode($this->linkSelector !== null ? $this->linkSelector : '#' . $id . ' a');
            $js .= "jQuery(document).pjax($linkSelector, \"#$id\", $options);";
        }
        if ($this->formSelector !== false) {
            $formSelector = Json::htmlEncode($this->formSelector !== null ? $this->formSelector : '#' . $id . ' form[data-pjax]');
            $js .= "\njQuery(document).on('submit', $formSelector, function (event) {jQuery.pjax.submit(event, '#$id', $options);});";
        }
        $view = $this->getView();
        PjaxAsset::register($view);
        if ($js !== '') {
            $view->registerJs($js);
        }
    }
}