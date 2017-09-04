<?php
use yii\helpers\Html;

$context = $this->context;
?>
<div class="row fileupload-buttonbar">
    <div class="col-lg-7">
        <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span><?= Yii::t('verbi', 'Add files') ?>...</span>
            <?php
                $name = $context->model instanceof \yii\base\Model && $context->attribute !== null ? Html::getInputName($context->model, $context->attribute) : $context->name;
                $value = $context->model instanceof \yii\base\Model && $context->attribute !== null ? Html::getAttributeValue($context->model, $context->attribute) : $context->value;
                echo Html::hiddenInput($name, $value).Html::fileInput($name, $value, $context->options);
            ?>
        </span>
    </div>
    <div class="col-lg-5 fileupload-progress">
        <div id="progress" class="progress">
            <div class="progress-bar progress-bar-success"></div>
        </div>
    </div>
</div>
<div id="files" class="files"></div>
        