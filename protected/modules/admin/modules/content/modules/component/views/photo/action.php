<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => get_class($model),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
  <?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= $confirmation ?></legend>
  <?php $this->renderPartial('detail', array(
    'model' => $model,
  )); ?>
</fieldset>
<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton', $button); ?>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
