<?php

use verbi\yii2Helpers\Html;
use verbi\yii2Helpers\widgets\ListView;
use yii\data\DataProviderInterface;
use verbi\yii2Helpers\widgets\Pjax;

$context = $this->context;

$js = '';

$enablePjax = true;
if (isset($context->enablePjax)) {
    $enablePjax = $context->enablePjax;
}
if ($enablePjax) {
    $pjaxConfig = [];
    if (isset($context->reloadTime)) {
        $pjaxConfig['reloadTime'] = $context->reloadTime;
    }
    $pjax = Pjax::begin($pjaxConfig);
    $context->clientOptions['done'] = new \yii\web\JsExpression('function (e, data) {'
            // refresh Pjax of files
            . '$.pjax.reload({container:"#' . $pjax->id . '"})'
            . '}');

//        $context->clientOptions['add'] = new \yii\web\JsExpression('function (e, data) {'
//                . 'if (e.isDefaultPrevented()) {'
//                    . 'return false;'
//                . '}'
//                . (isset($context->options['multiple']) && $context->options['multiple'] === false
//                ?'$(\'#' . $pjax->id . ' div.files div\').remove();':'')
//                . 'var $this = $(this),'
//                    . 'that = $this.data(\'blueimp-fileupload\') ||'
//                        . '$this.data(\'fileupload\'),'
//                    . 'options = that.options;'
//                . 'data.context = that._renderUpload(data.files)'
//                    . '.data(\'data\', data)'
//                    . '.addClass(\'processing\');'
//                . 'options.filesContainer['
//                    . 'options.prependFiles ? \'prepend\' : \'append\''
//                . '](data.context);'
//                . 'that._forceReflow(data.context);'
//                . 'that._transition(data.context);'
//                . 'data.process(function () {'
//                    . 'return $this.fileupload(\'process\', data);'
//                . '}).always(function () {'
//                    . 'data.context.each(function (index) {'
//                        . '$(this).find(\'.size\').text('
//                            . 'that._formatFileSize(data.files[index].size)'
//                        . ');'
//                    . '}).removeClass(\'processing\');'
//                    . 'that._renderPreviews(data);'
//                . '}).done(function () {'
//                    . 'data.context.find(\'.start\').prop(\'disabled\', false);'
//                    . 'if ((that._trigger(\'added\', e, data) !== false) &&'
//                            . '(options.autoUpload || data.autoUpload) &&'
//                            . 'data.autoUpload !== false) {'
//                        . 'data.submit();'
//                    . '}'
//                .'}).fail(function () {'
//                    . 'if (data.files.error) {'
//                        . 'data.context.each(function (index) {'
//                            . 'var error = data.files[index].error;'
//                            . 'if (error) {'
//                                . '$(this).find(\'.error\').text(error);'
//                            . '}'
//                        . '});'
//                    . '}'
//                . '});'
//            . '}');

}
?>
<div class="row fileupload-buttonbar">
    <div class="col-lg-7">
        <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span><?= Yii::t('verbi', 'Add files') ?>...</span>

            <?php
            $name = $context->model instanceof \yii\base\Model && $context->attribute !== null ? Html::getInputName($context->model, $context->attribute) : $context->name;
            $value = $context->model instanceof \yii\base\Model && $context->attribute !== null ? Html::getAttributeValue($context->model, $context->attribute) : $context->value;
            echo Html::hiddenInput($name, $value) . Html::fileInput($name, $value, $context->options);
            ?>

        </span>
        <button type="submit" class="btn btn-primary start">
            <i class="glyphicon glyphicon-upload"></i>
            <span><?= Yii::t('verbi', 'Start upload') ?></span>
        </button>
        <button type="reset" class="btn btn-warning cancel">
            <i class="glyphicon glyphicon-ban-circle"></i>
            <span><?= Yii::t('verbi', 'Cancel upload') ?></span>
        </button>
        <button type="button" class="btn btn-danger delete">
            <i class="glyphicon glyphicon-trash"></i>
            <span><?= Yii::t('verbi', 'Delete') ?></span>
        </button>
        <input type="checkbox" class="toggle">
        <span class="fileupload-process"></span>
    </div>
    <div class="col-lg-5 fileupload-progress fade">
        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
        </div>
        <div class="progress-extended">&nbsp;</div>
    </div>
</div>
<div class="files">
    <?php
    if ($context->files instanceof DataProviderInterface) {
        echo Html::div(ListView::widget([
            'dataProvider' => $context->files,
            'itemView' => 'file_list_view',
            'viewParams' => [
                'controller' => $context->controller,
            ],
            'summary' => '',
//            'events' => [
//                ListView::$EVENT_BEFORE_RENDER_ITEM => function ( GeneralFunctionEvent $event ) {
//                    $model = $event->params['model'];
//                    if(is_array($model)) {
//                        $model = new DynamicModel($model);
//                    }
//                    if($model instanceof Object
//                            || !$model->hasMethod('getBehaviors')) {
//                        throw new NotInstantiableException(
//                                'Invalid model type: a file must be a '
//                                . Object::className()
//                                . ' with behavior '
//                                . FileUploadModelBehavior::className());
//                    }
//
//                    $behaviors = array_filter($model->getBehaviors(), function($behavior) {
//                        return $behavior instanceof FileUploadModelBehavior;
//                    });
//                    if(!sizeof($behaviors)) {
//                        $behavior = FileUploadModelBehavior::className();
//                        $model->attachBehavior($behavior,$behavior);
//                    }
////                    $event->params['model'] =& $model;
//                }
//            ],
        ]),['class'=>'preloaded']);
    }

//    foreach ($context->files as $file) {
//        echo Html::div(
//                Html::div(Html::tag('span', Html::a(Html::img($file['thumbnailUrl'])
//                                        , $file['url']
//                                        , [
//                                    'title' => $file['name'],
//                                    'download' => $file['name'],
//                                    'data-gallery' => '1',
//                                ]), ['class' => 'preview']), ['class' => 'col-md-2'])
//                . Html::div(
//                        Html::tag('p', (isset($file['url']) ? Html::a($file['name'], $file['url']
//                                                , [
//                                            'title' => $file['name'],
//                                            'download' => $file['name'],
//                                            'data-gallery' => '1',
//                                        ]) :
//                                        Html::tag('span', $file['name'])
//                                )
//                                , ['class' => 'name'])
////                        . '{% if (file.error) { %}'
////                        . Html::div(
////                                Html::tag('span', Yii::t('verbi', 'Error'), ['class' => 'error text-danger'])
////                                . '{%=file.error%}'
////                        )
////                        . '{% } %}'
//                        , ['class' => 'col-md-5'])
//                . Html::div(
//                        Html::tag('span', $file['size'], ['class' => 'size'])
//                        , ['class' => 'col-md-2'])
//                . Html::div(
//                        Html::tag('button', Html::tag('i', '', ['class' => 'glyphicon glyphicon-trash'])
//                                . Html::tag('span', Yii::t('verbi', 'Delete'))
//                                , [ 'class' => 'btn btn-primary delete',])
//                        
//                        , ['class' => 'col-md-3'])
//                , ['class' => 'row template-download']);
//    }
    ?>
</div>
<?php
if ($enablePjax) {
    Pjax::end();
}

if ($js) {
    $pjax->view->registerJs($js);
}