<?php $this->cs->registerScriptFile($this->coreScriptUrl('dialogs'), CClientScript::POS_END); ?>

<div class="container">
  <h2><?= $title ?></h2>
</div>

<?php Yii::app()->editor->renderControls($model); ?>

<div id="iframe-wrapper" style="margin: 10px auto 0; height: auto; padding: 0; border-top: 1px solid rgba(127,127,127,0.5);">
  <?=
  CHtml::tag('iframe', array(
    'src' => $this->createUrl('/render/page', array(
      'id' => $model->id,
      'edit' => 'content',
      'lng' => Yii::app()->getLanguage(),
    )),
    'style' => implode(';', array(
      'width:100%',
      'height:100%',
      'border:0',
      'margin:0',
      'padding:0',
      'overflow:auto',
    )),
    ), '')
  ?>
</div>
