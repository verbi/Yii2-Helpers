<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <?php
    use verbi\yii2Helpers\Html;
    echo Html::div(
            Html::div(Html::tag('span',
                    '{% if (file.thumbnailUrl) { %}'
                    . Html::a(Html::img('{%=file.thumbnailUrl%}', ['lazyLoad' => false,])
                            , '{%=file.url%}'
                            , [
                                'title' => '{%=file.name%}',
                                'download' => '{%=file.name%}',
                                'data-gallery' => '1',
                            ])
                    . '{% } %}',
                    ['class'=>'preview']),['class'=>'col-md-2'])
            . Html::div(
                    Html::tag('p',
                            '{% if (file.url) { %}'
                            . Html::a('{%=file.name%}', '{%=file.url%}'
                                    , [
                                        'title' => '{%=file.name%}',
                                        'download' => '{%=file.name%}',
                                        'data-gallery' => '1',
                                    ])
                            . '{% } else { %}'
                            . Html::tag('span', '{%=file.name%}')
                            . '{% } %}'
                                    ,['class' => 'name'])
                    . '{% if (file.error) { %}'
                    . Html::div(
                            Html::tag('span',Yii::t('verbi', 'Error'),['class'=>'error text-danger'])
                            . '{%=file.error%}'
                            )
                    . '{% } %}'
                ,['class'=>'col-md-5'])
            . Html::div(
                    Html::tag('span','{%=o.formatFileSize(file.size)%}',['class' => 'size'])
                    ,['class'=>'col-md-2'])
            . Html::div(
                    '{% if (file.deleteUrl) { %}'
                        . Html::tag('button', 
                                Html::tag('i', '', ['class' => 'glyphicon glyphicon-trash'])
                                . Html::tag('span', Yii::t('verbi', 'Delete'))
                                , [ 'class' => 'btn btn-primary delete',])
                    . '{% } else { %}'
                        . Html::tag('button',
                                Html::tag('i', '', ['class' => 'glyphicon glyphicon-ban-circle'])
                                . Html::tag('span', Yii::t('verbi', 'Cancel'))
                                , [ 'class' => 'btn btn-warning cancel', ])
                    . '{% } %}'
                    ,['class'=>'col-md-3'])
    ,['class' => 'row template-download fade']);
    ?>
{% } %}
</script>
