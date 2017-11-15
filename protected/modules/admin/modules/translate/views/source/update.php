<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => $model->getGridID(),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
  <?= $form->textFieldRow($model, 'module', array('class' => 'input-block-level')) ?>
  <?= $form->textFieldRow($model, 'category', array('class' => 'input-block-level')) ?>
  <?= $form->textFieldRow($model, 'source', array('class' => 'input-block-level')) ?>
</fieldset>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
