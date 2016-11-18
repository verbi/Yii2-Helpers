<?php

namespace verbi\yii2Helpers\widgets;
use \verbi\yii2Helpers\widgets\base\Config;
use \verbi\yii2Helpers\Html;
use \yii\base\InvalidConfigException;
use kartik\popover\PopoverX;
use yii\widgets\ActiveForm;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class Editable extends \kartik\editable\Editable {
    use \verbi\yii2Helpers\traits\WidgetTrait;
    const INPUT_PHONE = '\borales\extensions\phoneInput\PhoneInput';
    const INPUT_RATING = 'verbi\yii2Helpers\widgets\StarRating';

    protected $formatArray = [
        'datetime' => self::INPUT_DATETIME,
        'date' => self::INPUT_DATE,
        'time' => self::INPUT_TIME,
        'boolean' => self::INPUT_SWITCH,
        'password' => self::INPUT_PASSWORD,
        'money' => self::INPUT_MONEY,
        'color' => self::INPUT_COLOR,
        'file' => self::INPUT_FILEINPUT,
        'image' => self::INPUT_FILEINPUT,
        'rating' => self::INPUT_RATING,
        'range' => self::INPUT_RANGE,
        'slider' => self::INPUT_SLIDER,
        'phone' => self::INPUT_PHONE,
    ];

    public function init() {
        $this->format = Editable::FORMAT_BUTTON;
        $this->formOptions['action'] = 'http://localhost/goestingWeb/web/api/profiles/qsdf';
        $this->formOptions['method'] = 'put';

        $this->options['options']['name']=$this->attribute;
        
        $explodedNameSpace = explode( '\\', $this->model->className() );
        $modelClass = array_pop( $explodedNameSpace );
        
        $this->inlineSettings = [
            ];
        
        
        \verbi\yii2Helpers\widgets\assets\Oauth2Asset::register($this->getView());
        $this->pluginEvents['editableBeforeSubmit'] = 'function( el, jqXHR ) {'
                . 'var oauth2 = new Oauth2(' . json_encode(['siteBaseUrl' => \Yii::$app->homeUrl,]) . ');'
                . 'jqXHR.setRequestHeader(\'Authorization\',\'Bearer \' + oauth2.getAjaxAccessToken());'
                . 'return true;'
            . '}';
        
        $this->inputType = $this->initInputType();
        parent::init();
    }

    protected function initInputType() {
        $format = 'text';
        if (isset($this->format) && false) {
            $format = $this->format;
        } elseif (isset($this->attribute) && isset($this->model) && $this->model->hasMethod('getAttributeFormat')) {
            $format = $this->model->getAttributeFormat($this->attribute);
        }
        
        if (isset($this->formatArray[$format])) {
            return $this->formatArray[$format];
        }
        return self::INPUT_TEXT;
    }

    /**
     * Initializes the widget
     *
     * @throws InvalidConfigException
     */
    protected function initEditable()
    {
        if (empty($this->inputType)) {
            throw new InvalidConfigException("The 'type' of editable input must be set.");
        }
        if (!Config::isValidInput($this->inputType)) {
            throw new InvalidConfigException("Invalid input type '{$this->inputType}'.");
        }
        if ($this->inputType === self::INPUT_WIDGET && empty($this->widgetClass)) {
            throw new InvalidConfigException("The 'widgetClass' must be set when the 'inputType' is set to 'widget'.");
        }
        if (Config::isDropdownInput($this->inputType) && !isset($this->data)) {
            throw new InvalidConfigException("You must set the 'data' property for '{$this->inputType}'.");
        }
        if (!empty($this->formClass) && !class_exists($this->formClass)) {
            throw new InvalidConfigException("The form class '{$this->formClass}' does not exist.");
        }
        Config::validateInputWidget($this->inputType);
        $this->initI18N(__DIR__);
        $this->initOptions();
        $this->_popoverOptions['options']['id'] = $this->options['id'] . '-popover';
        $this->_popoverOptions['toggleButton']['id'] = $this->options['id'] . '-targ';
        $this->registerAssets();
        echo Html::beginTag('div', $this->containerOptions);
        if ($this->format == self::FORMAT_BUTTON) {
            echo Html::tag('div', $this->displayValue, $this->editableValueOptions);
        }
        if ($this->asPopover === true) {
            PopoverX::begin($this->_popoverOptions);
        } elseif ($this->format !== self::FORMAT_BUTTON) {
            echo $this->renderToggleButton();
        }
        echo Html::beginTag('div', $this->contentOptions);
        
        /**
         * @var ActiveForm $class
         */
        $class = $this->formClass;
        $this->_form = $class::begin($this->formOptions);
        if (!$this->_form instanceof ActiveForm) {
            throw new InvalidConfigException("The form class '{$class}' MUST extend from '\yii\widgets\ActiveForm'.");
        }
    }

    /**
     * Renders the editable form fields
     *
     * @return string
     */
    protected function renderFormFields()
    {
        echo $this->parseTemplate('templateBefore');
        echo Html::hiddenInput('hasEditable', 0) . "\n";
        $before = $this->beforeInput;
        $after = $this->afterInput;
        if ($before !== null && is_string($before) || is_callable($before)) {
            echo (is_callable($before) ? call_user_func($before, $this->_form, $this) : $before) . "\n";
        }
        if ($this->inputType === self::INPUT_HTML5_INPUT) {
            echo $this->renderHtml5Input() . "\n";
        } elseif ($this->inputType === self::INPUT_WIDGET) {
            echo $this->renderWidget($this->widgetClass) . "\n";
        } elseif (Config::isHtmlInput($this->inputType)) {
            echo $this->renderInput() . "\n";
        } elseif (Config::isInputWidget($this->inputType)) {
            echo $this->renderWidget($this->inputType) . "\n";
        }
        if ($after !== null && is_string($after) || is_callable($after)) {
            echo (is_callable($after) ? call_user_func($after, $this->_form, $this) : $after) . "\n";
        }
        echo $this->parseTemplate('templateAfter');
    }
}
