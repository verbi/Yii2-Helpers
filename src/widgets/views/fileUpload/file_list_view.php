<?php

use verbi\yii2Helpers\Html;
use yii\base\DynamicModel;
use yii\base\Object;
use yii\di\NotInstantiableException;
use verbi\yii2Helpers\behaviors\fileUpload\FileUploadModelBehavior;

if (is_array($model)) {
    $model = new DynamicModel($model);
}
if ($model instanceof Object || !$model->hasMethod('getBehaviors')) {
    throw new NotInstantiableException(
    'Invalid model type: a file must be a '
    . Object::className()
    . ' with behavior '
    . FileUploadModelBehavior::className());
}

$behaviors = array_filter($model->getBehaviors(), function($behavior) {
    return $behavior instanceof FileUploadModelBehavior;
});
if (!sizeof($behaviors)) {
    $behavior = FileUploadModelBehavior::className();
    $model->attachBehavior($behavior, $behavior);
}



echo Html::div(
        Html::div(Html::tag('span', Html::a(Html::img($model->getThumbnailUrl())
                                , $model['url']
                                , [
                            'title' => $model['name'],
                            'download' => $model['name'],
                            'data-gallery' => '1',
                        ]), ['class' => 'preview']), ['class' => 'col-md-2'])
        . Html::div(
                Html::tag('p', (isset($model['url']) ? Html::a($model['name'], $model['url']
                                        , [
                                    'title' => $model['name'],
                                    'download' => $model['name'],
                                    'data-gallery' => '1',
                                ]) :
                                Html::tag('span', $model['name'])
                        )
                        , ['class' => 'name'])
//                        . '{% if (file.error) { %}'
//                        . Html::div(
//                                Html::tag('span', Yii::t('verbi', 'Error'), ['class' => 'error text-danger'])
//                                . '{%=file.error%}'
//                        )
//                        . '{% } %}'
                , ['class' => 'col-md-5'])
        . Html::div(
                Html::tag('span', $model->getSize(), ['class' => 'size'])
                , ['class' => 'col-md-2'])
        . Html::div(
                Html::tag('button', Html::tag('i', '', ['class' => 'glyphicon glyphicon-trash'])
                        . Html::tag('span', Yii::t('verbi', 'Delete'))
                        , [ 'class' => 'btn btn-primary delete',])
                , ['class' => 'col-md-3'])
        , ['class' => 'row template-download']);
