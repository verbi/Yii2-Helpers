<?php
namespace verbi\yii2Helpers\widgets;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/yii2-extended-activerecord/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class ListView extends \yii\widgets\ListView {
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
    
    public $restUrl;
    
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
            $pjaxConfig = [];
            if( isset( $config['reloadTime'] ) ) {
                $pjaxConfig['reloadTime'] = $config['reloadTime'];
                unset( $config['reloadTime'] );
            }
            $pjax = Pjax::begin( $pjaxConfig );
            $pjax->linkSelector = '#' . $pjax->getId() . ' .pagination a';
            echo parent::widget( $config );
            Pjax::end();
        }
        if($js) {
            $pjax->view->registerJs( $js );
        }
        return ob_get_clean();
    }
}