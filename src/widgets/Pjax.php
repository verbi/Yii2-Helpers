<?php

namespace verbi\yii2Helpers\widgets;

use Yii;
use yii\widgets\Spaceless;
use yii\helpers\Json;
use verbi\yii2Helpers\widgets\assets\PjaxAsset;
use verbi\yii2Helpers\Html;
use verbi\yii2WebView\web\View;
use yii\web\JqueryAsset;
use verbi\yii2Helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class Pjax extends \yii\widgets\Pjax {

    protected $_id = null;
    public static $counter = [];
    public $clientOptions = [
        'skipOuterContainers' => true
    ];
    protected $_reloadTime;
    public $css;
    public $cssFiles;
    public $js;
    public $jsFiles;
    public $linkTags;
    public $assetBundles = [];
    protected $_assetManager;
    public $timeout = 10000;
    public $progressBar = true;

    public function getId($autoGenerate = true) {
        if ($autoGenerate && $this->_id === null) {
            $uniqueId = str_replace('/', '-', \Yii::$app->controller->action->uniqueId);
            if (!isset(static::$counter[$uniqueId])) {
                static::$counter[$uniqueId] = 0;
            }
            $this->_id = static::$autoIdPrefix . '-pjax-' . $uniqueId . '-' . static::$counter[$uniqueId] ++;
        }
        return $this->_id;
    }

    public function setId($value) {
        $this->_id = $value;
    }

    public static function begin($config = []) {
        $reloadTime = null;
        if (isset($config['reloadTime'])) {
            $reloadTime = $config['reloadTime'];
            unset($config['reloadTime']);
        }
        $return = parent::begin($config);
        if ($reloadTime) {
            $return->_reloadTime = $reloadTime;
        }
        Spaceless::begin();
        return $return;
    }

    public static function end() {
        $js = '';
        Spaceless::end();
        $return = parent::end();
        if ($return->_reloadTime) {
            $js .= 'var oauth2 = new Oauth2(' . json_encode(['siteBaseUrl' => \Yii::$app->homeUrl,]) . ');'
                    . 'setInterval(function(){ $.pjax.reload('
                    . '{container: "#'
                    . $return->id
                    . '", async:true'
                    . ((!\yii::$app->user->isGuest) ? ',headers: {'
                            . '"Authorization":"Bearer " + oauth2.getAjaxAccessToken()'
                            . '},' : '')
                    . '});},'
                    . ($return->_reloadTime * 1000)
                    . ');';
        }
        $return->view->registerJs($js);
        return $return;
    }

    protected function renderPjaxEndHtml() {
        $array = [];
        if ($this->requiresPjax()) {
            foreach (array_keys($this->assetBundles) as $bundle) {
                $this->registerAssetFiles($bundle);
            }
            if ($this->jsFiles) {
                foreach ($this->jsFiles as $position => $jsfiles) {
                    if ($jsfiles) {
                        $array[$position]['jsFiles'] = $jsfiles;
                    }
                }
            }
            $scripts = [];
            if ($this->js) {
                foreach ($this->js as $position => $js) {
                    if ($js) {
                        $array[$position]['js'] = implode("", $js);
                    }
                }
            }
            if ($this->cssFiles) {
                foreach ($this->cssFiles as $key => $cssFile) {
                    if ($cssFile) {
                        $array['cssFiles'][$key] = $cssFile;
                    }
                }
            }
            if ($this->css) {
                foreach ($this->css as $key => $css) {
                    if ($css) {
                        $array['css'][$key] = $css;
                    }
                }
            }
        }
        $content = Html::hidden(json_encode($array, true), ['class' => 'hidden js-pjax-scripts']);
        return $content;
    }

    public function clear() {
        $this->linkTags = null;
        $this->css = null;
        $this->cssFiles = null;
        $this->js = null;
        $this->jsFiles = null;
        $this->assetBundles = [];
    }

    public function getAssetManager() {
        return $this->_assetManager ? : Yii::$app->getAssetManager();
    }

    protected function registerAssetFiles($name) {
        if (!isset($this->assetBundles[$name])) {
            return;
        }
        $bundle = $this->assetBundles[$name];
        if ($bundle) {
            foreach ($bundle->depends as $dep) {
                $this->registerAssetFiles($dep);
            }
            $bundle->registerAssetFiles($this);
        }
        unset($this->assetBundles[$name]);
    }

    public function registerAssetBundle($name, $position = null) {
        if (!isset($this->assetBundles[$name])) {
            $am = $this->getAssetManager();
            $bundle = $am->getBundle($name);
            $this->assetBundles[$name] = false;
            // register dependencies
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
            $this->assetBundles[$name] = $bundle;
        } elseif ($this->assetBundles[$name] === false) {
            throw new InvalidConfigException("A circular dependency is detected for bundle '$name'.");
        } else {
            $bundle = $this->assetBundles[$name];
        }
        if ($position !== null) {
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            if ($pos === null) {
                $bundle->jsOptions['position'] = $pos = $position;
            } elseif ($pos > $position) {
                throw new InvalidConfigException("An asset bundle that depends on '$name' has a higher javascript file position configured than '$name'.");
            }
            // update position for all dependencies
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
        }
        return $bundle;
    }

    public function registerLinkTag($options, $key = null) {
        if ($key === null) {
            $this->linkTags[] = Html::tag('link', '', $options);
        } else {
            $this->linkTags[$key] = Html::tag('link', '', $options);
        }
    }

    public function registerCss($css, $options = [], $key = null) {
        $key = $key ? : md5($css);
        $this->css[$key] = [
            'css' => $css,
            'options' => $options,
        ];
    }

    public function registerCssFile($url, $options = [], $key = null) {
        $url = Yii::getAlias($url);
        $key = $key ? : $url;
        $depends = ArrayHelper::remove($options, 'depends', []);
        if (empty($depends)) {
            $this->cssFiles[$key] = [
                'url' => $url,
                'options' => $options,
            ];
        } else {
            $this->getAssetManager()->bundles[$key] = Yii::createObject([
                        'class' => AssetBundle::className(),
                        'baseUrl' => '',
                        'css' => [strncmp($url, '//', 2) === 0 ? $url : ltrim($url, '/')],
                        'cssOptions' => $options,
                        'depends' => (array) $depends,
            ]);
            $this->registerAssetBundle($key);
        }
    }

    public function registerJs($js, $position = View::POS_READY, $key = null) {
        $key = $key ? : md5($js);
        $this->js[$position][$key] = $js;
        if ($position === View::POS_READY || $position === View::POS_LOAD) {
            JqueryAsset::register($this);
        }
    }

    public function registerJsFile($url, $options = [], $key = null) {
        $url = Yii::getAlias($url);
        $key = $key ? : $url;
        $depends = ArrayHelper::remove($options, 'depends', []);
        if (empty($depends)) {
            $position = ArrayHelper::remove($options, 'position', View::POS_END);
            $this->jsFiles[$position][$key] = [
                'url' => $url,
                'options' => $options
            ];
        } else {
            $this->getAssetManager()->bundles[$key] = Yii::createObject([
                        'class' => AssetBundle::className(),
                        'baseUrl' => '',
                        'js' => [strncmp($url, '//', 2) === 0 ? $url : ltrim($url, '/')],
                        'jsOptions' => $options,
                        'depends' => (array) $depends,
            ]);
            $this->registerAssetBundle($key);
        }
    }

    public function registerClientScript() {
        if (!\yii::$app->user->isGuest) {
            if (!isset($this->clientOptions['headers'])) {
                $this->clientOptions['headers'] = [];
            }
            $this->clientOptions['headers']['Authorization'] = new JsExpression(' ('
                    . '"Bearer " + (new Oauth2(' . json_encode(['siteBaseUrl' => \Yii::$app->homeUrl,]) . ')).getAjaxAccessToken()'
                    . ')');
        }
        $id = $this->options['id'];
        $this->clientOptions['headers']['X-PJAX'] = 'true';
        $this->clientOptions['headers']['X-PJAX-Container'] = '#' . $id;


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
            $js .= "jQuery(document).on('submit', $formSelector, function (event) {jQuery.pjax.submit(event, '#$id', $options);});";
        }

        $view = $this->getView();
        PjaxAsset::register($view);
        if ($js !== '') {
            $js.= "dynamicScriptloader=new PjaxDynamicScriptLoader();dynamicScriptloader.init(\"#$id\");";
            $view->registerJs($js);
        }
    }

    /**
     * @inheritdoc
     */
    public function run() {
        if($this->progressBar==true
                || is_array($this->progressBar)
                && (!isset($this->progressBar['enabled'])
                    || $this->progressBar['enabled'])) {
            \verbi\yii2Helpers\widgets\assets\NProgressAsset::register($this->getView());
            $js = '$(document)'
                    . '.ajaxStart(function () {'
                        . 'NProgress.start();'
                    . '})'
                    . '.ajaxStop(function () {'
                        . 'NProgress.done();'
                    . '});';
            if(is_array($this->progressBar) && isset($this->progressBar['js'])) {
                $js = $this->progressBar['js'];
            }
            if($js) {
                $this->getView()->registerJs($js);
            }
        }
        if ($this->requiresPjax()) {
            Spaceless::begin();
            echo $this->renderPjaxEndHtml();
            Spaceless::end();
            $view = $this->getView();
            $view->clear();
        }
        return parent::run();
    }

    
}
