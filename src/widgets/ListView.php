<?php
namespace verbi\yii2Helpers\widgets;

use verbi\yii2Helpers\events\GeneralFunctionEvent;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class ListView extends \yii\widgets\ListView {
    use \verbi\yii2Helpers\traits\WidgetTrait;
    
    public static $EVENT_BEFORE_RENDER_ITEM = 'beforeRenderItem';
    
    public $pager = [
        'class' => '\verbi\yii2Helpers\widgets\LinkPager',
    ];
    
    public static $restItemView = null;
    
    public static function getRestItemView() {
        return self::$restItemView;
    }
    
    public function setRestItemView($restItemView) {
        self::$restItemView = $restItemView;
    }
    
    public $events = [];
    
    public $restUrl;
    
//    public function renderItems()
//    {
//        $models = $this->dataProvider->getModels();
//        $this->eventAfterGetModels(&$models);
//        return parent::renderItems();
//    }
//    
//    protected function eventAfterGetModels($models) {
//        $event = new GeneralFunctionEvent();
//        $event->setParams(['models'=>$models]);
//        $this->trigger(static::$EVENT_AFTER_GET_MODELS,$event);
//    }
    
    
//    
//    /**
//     * Renders all data models.
//     * @return string the rendering result
//     */
//    public function renderItems()
//    {
//        $models = $this->dataProvider->getModels();
////        $models =& $models2;
//        $keys = $this->dataProvider->getKeys();
//        $rows = [];
//        foreach (array_values($models) as $index => &$model) {
//            $key = $keys[$index];
//            if (($before = $this->renderBeforeItem($model, $key, $index)) !== null) {
//                $rows[] = $before;
//            }
//            $rows[] = $this->renderItem($model, $key, $index);
//            if (($after = $this->renderAfterItem($model, $key, $index)) !== null) {
//                $rows[] = $after;
//            }
//        }
//        return implode($this->separator, $rows);
//    }
//    
//    protected function renderBeforeItem(&$model, $key, $index)
//    {
//        return self::renderBeforeItem($model, $key, $index);
//    }
    
    public function init()
    {
        parent::init();
        $this->initEvents();
    }

    protected function initEvents() {
        foreach($this->events as $name => $function) {
            $this->on($name, $function);
        }
    }
    
    public function renderItem($model, $key, $index)
    {
        if($this->beforeRenderItem($model)) {
            return parent::renderItem($model, $key, $index);
        }
        return false;
    }
    
    protected function beforeRenderItem(&$model) {
        $event = new GeneralFunctionEvent();
        $event->setParams(['model' => &$model]);
        $this->trigger(static::$EVENT_BEFORE_RENDER_ITEM,$event);
        return $event->isValid;
    }
    
    public static function widget($config = [])
    {
        if(
                !isset($config['options'])
                || !isset($config['options']['tag'])
        ) {
            $config['options']['tag'] = false;
        }
        
        if(
                !isset($config['itemOptions'])
                || !isset($config['itemOptions']['tag'])
        ) {
            $config['itemOptions']['tag'] = false;
        }
        
        
        $js = '';
        ob_start();
        ob_implicit_flush( false );
        if( self::getRestItemView() ) {
            $restConfig = [];
            if( isset( $config['reloadTime'] ) ) {
                $restConfig['reloadTime'] = $config['reloadTime'];
                unset( $config['reloadTime'] );
                
                \Yii::$app->getView();
            }
            echo parent::widget( $config );
        }
        else {
            $enablePjax = true;
            if(isset($config['enablePjax'])) {
                $enablePjax = $config['enablePjax'];
                unset($config['enablePjax']);
            }
            if($enablePjax) {
                $pjaxConfig = [];
                if( isset( $config['reloadTime'] ) ) {
                    $pjaxConfig['reloadTime'] = $config['reloadTime'];
                    unset( $config['reloadTime'] );
                }
                $pjax = Pjax::begin( $pjaxConfig );
                $pjax->linkSelector = '#' . $pjax->getId() . ' .pagination a';
            }
            echo parent::widget( $config );
            if($enablePjax) {
                Pjax::end();
            }
        }
        if($js) {
            $pjax->view->registerJs( $js );
        }
        return ob_get_clean();
    }
}