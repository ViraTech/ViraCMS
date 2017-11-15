<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'horizontal',
)); ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
  <?= $form->uneditableRow($model, 'id', array('class' => 'span1')) ?>
  <?= $form->uneditableRow($model, 'active', array('class' => 'span1', 'type' => 'boolean')) ?>
  <?= $form->uneditableRow($model, 'title', array('class' => 'span7')) ?>
  <?= $form->uneditableRow($model, 'locale', array('class' => 'span2')) ?>
  <?= $form->uneditableRow($model, 'index', array('class' => 'span1')) ?>
</fieldset>
<div class="form-actions">
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
