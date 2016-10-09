<?php
use \verbi\yii2Helpers\Html;
if( !isset( $routeParams ) ) {
    $routeParams = [];
}

echo Html::panel(
        [
        'heading'=>Html::a(
                $model->id,
                array_merge(
                    [
                        '/' . $model->getPath(),
                    ],
                    $routeParams
                        
                )
        ),
            'body'=>$model->runChildAction('', $routeParams,$this)
            ]
);
