<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'horizontal',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
    'enctype' => 'multipart/form-data',
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= Yii::t('admin.translate.titles', 'Upload Translated Messages') ?></legend>
  <?= $form->dropDownListRow($model, 'languageID', $languages) ?>
  <?= $form->fileFieldRow($model, 'file') ?>
  <?= $form->dropDownListRow($model, 'encoding', array_combine($model->getAvailableEncodings(), $model->getAvailableEncodings())) ?>
</fieldset>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('admin.translate.labels', 'Uploading...')) ?>"><i class="icon-upload-alt"></i> <?= Yii::t('common', 'Upload') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
