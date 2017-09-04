<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <?php
    use verbi\yii2Helpers\Html;
    echo Html::div(
            Html::div(Html::tag('span', '',['class'=>'preview']),['class'=>'col-md-2'])
            . Html::div(
                    Html::tag('p','{%=file.name%}',['class' => 'name'])
                    . Html::tag('strong','',['class'=>'error text-danger'])
                ,['class'=>'col-md-5'])
            . Html::div(
                    Html::tag('p',Yii::t('verbi', 'Processing').'...',['class' => 'size'])
                    . Html::div(
                            Html::div('', ['class' => 'progress-bar progress-bar-success', 'style' => 'width:0%;'])
                            , [ 'class' => 'progress progress-striped active',
                                'role' => 'progressbar',
                                'aria-valuemin' => '0',
                                'aria-valuemax' => '100',
                                'aria-valuenow' => '0' ]
                            )
                    ,['class'=>'col-md-2'])
            . Html::div(
                    '{% if (!i && !o.options.autoUpload) { %}'
                        . Html::tag('button', 
                                Html::tag('i', '', ['class' => 'glyphicon glyphicon-upload'])
                                . Html::tag('span', Yii::t('verbi', 'Start'))
                                , [ 'class' => 'btn btn-primary start', 'disabled' => '1', ])
                    . '{% } %}'
                    . '{% if (!i) { %}'
                        . Html::tag('button', 
                                Html::tag('i', '', ['class' => 'glyphicon glyphicon-ban-circle'])
                                . Html::tag('span', Yii::t('verbi', 'Cancel'))
                                , [ 'class' => 'btn btn-warning cancel', ])
                    . '{% } %}'
                    ,['class'=>'col-md-3'])
    ,['class' => 'row template-upload fade']);
    ?>
{% } %}
</script>