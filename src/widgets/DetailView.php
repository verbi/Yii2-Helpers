<?php
namespace verbi\yii2Helpers\widgets;

use verbi\yii2Helpers\Html;
use Yii\helpers\ArrayHelper;
/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class DetailView extends \yii\widgets\DetailView {//\kartik\detail\DetailView {
    /**
     * @var array the HTML attributes for the container tag of this widget. The "tag" option specifies
     * what container tag should be used. It defaults to "table" if not set.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'detail-view'];
    
    public function __construct($config = []) {
        $this->template = Html::tag('div', Html::tag('div',Html::label('{label}'),['class'=>'col-md-6']).Html::tag('div','{value}',['class'=>'col-md-6']),['class'=>'row']);
        parent::__construct($config);
    }
    
    /**
     * Renders the detail view.
     * This is the main entry of the whole detail view rendering.
     */
    public function run()
    {
        $rows = [];
        $i = 0;
        foreach ($this->attributes as $attribute) {
            $rows[] = $this->renderAttribute($attribute, $i++);
        }
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        echo Html::tag($tag, implode("\n", $rows), $options);
    }
    
    /**
     * Normalizes the attribute specifications.
     * @throws InvalidConfigException
     */
    protected function normalizeAttributes()
    {
        if ($this->attributes === null) {
            if ($this->model instanceof Model) {
                $this->attributes = $this->model->attributes();
            } elseif (is_object($this->model)) {
                $this->attributes = $this->model instanceof Arrayable ? array_keys($this->model->toArray()) : array_keys(get_object_vars($this->model));
            } elseif (is_array($this->model)) {
                $this->attributes = array_keys($this->model);
            } else {
                throw new InvalidConfigException('The "model" property must be either an array or an object.');
            }
            sort($this->attributes);
        }
        foreach ($this->attributes as $i => $attribute) {
            if (is_string($attribute)) {
                if (preg_match('/^([\w]+)$/', $attribute)) {
                    if(
                        $this->model->hasMethod('getAttributeFormat')
                        && ($format = $this->model->getAttributeFormat($attribute))
                    ) {
                        $this->attributes[$i] = $attribute .':'.$format;
                    }
                }
            }
        }
        parent::normalizeAttributes();
    }
    
    /**
     * Renders a single attribute.
     * @param array $attribute the specification of the attribute to be rendered.
     * @param integer $index the zero-based index of the attribute in the [[attributes]] array
     * @return string the rendering result
     */
    protected function renderAttribute($attribute, $index)
    {
        //die(print_r($attribute,true));
        if (is_string($this->template)) {
            $value = $this->formatter->format($attribute['value'], $attribute['format']);
            if($this->model && isset($attribute['attribute'])) {
                $value = Editable::widget([
                    'model'=>$this->model, 
                    'attribute' => $attribute['attribute'],
                    'size' => 'md',
                    'format' => $attribute['format'],
                    'editableValueOptions'=>['class'=>'well well-sm'],
                    'asPopover' => false,
                ]);
                $attribute['format'] = 'raw';
            }
            return strtr($this->template, [
                '{label}' => $attribute['label'],
                '{value}' => $value,
            ]);
        } else {
            return call_user_func($this->template, $attribute, $index, $this);
        }
    }
}