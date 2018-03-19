<?php

namespace verbi\yii2Helpers\widgets;

use Yii;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */

class ActiveForm extends \yii\widgets\ActiveForm {

    public $fieldClass = 'verbi\yii2Helpers\widgets\ActiveField';
    protected static $_baseRestOptions = [
        'action' => 'create',
        'ajaxOptions' => [
            'url' => null,
        ],
        'itemView' => null,
    ];
    public static $restOptions = [];

    public static function begin($config = []) {
        $restOptions = array_merge(static::$_baseRestOptions, self::$restOptions);
        if (isset($config['restOptions'])) {
            $restOptions = array_merge($restOptions, $config['restOptions']);
            unset($config['restOptions']);
            //die();
        }
//        $regenerateCsrf = true;
//        if (isset($config['regenerate_csrf'])) {
//            $regenerateCsrf = $config['regenerate_csrf'];
//            unset($config['regenerate_csrf']);
//        }
//        if ($regenerateCsrf) {
//            Yii::$app->getRequest()->getCsrfToken(true);
//        }
        $form = parent::begin($config);

        if ($restOptions['itemView'] && isset($restOptions['ajaxOptions']['url']) && $restOptions['ajaxOptions']['url']) {
            $ajaxSuccess = null;
            if (isset($restOptions['ajaxOptions']['success'])) {
                $ajaxSuccess = $restOptions['ajaxOptions']['success'];
                unset($restOptions['ajaxOptions']['success']);
            }

            \verbi\yii2Helpers\widgets\assets\RestAsset::register($form->getView());
            $js = '$(\'form#' . $form->id . '\').on(\'beforeSubmit\', function(e) {'
                    . 'var form = $(this);'
                    . 'var rest = new Rest();'
                    . 'var itemView = ' . $restOptions['itemView'] . ';'
                    . 'var options = ' . json_encode($restOptions['ajaxOptions']) . ';'
                    . 'options.data = form.serialize();'
                    . ($ajaxSuccess ? 'options.success=' . $ajaxSuccess . ';' : '')
                    . 'rest.create(options);'
                    . 'return false;'
                    . '});';

            $form->getView()->registerJs($js);
        }
        return $form;
    }

}
