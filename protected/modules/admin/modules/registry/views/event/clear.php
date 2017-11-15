<?php $this->renderPartial('header'); ?>
<?php $this->widget('bootstrap.widgets.TbAlert'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'vertical',
)); ?>
<fieldset>
  <legend><?= Yii::t('admin.registry.messages', 'Are you sure you want clear entire log?') ?></legend>
</fieldset>
<button class="btn btn-danger" type="submit"><i class="icon-trash"></i> <?= Yii::t('admin.registry.labels', 'Clear Log') ?></button>
<a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
<?php $this->endWidget(); ?>
