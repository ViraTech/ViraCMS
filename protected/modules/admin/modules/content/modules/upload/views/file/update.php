<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => $model->getGridID(),
  'type' => 'horizontal',
  'htmlOptions' => array(
    'enctype' => 'multipart/form-data',
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<?= $form->hiddenField($model, 'id') ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
  <?= $form->dropDownListRow($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'), array('class' => 'input-block-level')) ?>
  <?php $this->widget('ext.eselect2.ESelect2', array(
    'selector' => '#' . CHtml::activeId($model, 'siteID'),
  )); ?>
  <?php if (!$model->isNewRecord): ?>
    <?= $form->uneditableRow($model, 'filename', array('class' => 'input-block-level')) ?>
    <?= $form->uneditableRow($model, 'url', array('class' => 'input-block-level', 'value' => $model->getUrl())) ?>
    <?= $form->uneditableRow($model, 'mime', array('class' => 'input-block-level')) ?>
    <?= $form->uneditableRow($model, 'size') ?>
  <?php endif; ?>
  <?= $form->fileFieldRow($model, 'upload', array('hint' => Yii::t('common', 'Maximum upload file size is {size} (set in php.ini)', array('{size}' => Yii::app()->format->formatSize($maxSize))))) ?>
  <?= $form->textFieldRow($model, 'comment', array('class' => 'input-block-level')) ?>
</fieldset>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('admin.content.labels', 'Uploading...')) ?>"><i class="icon-upload-alt"></i> <?= Yii::t('admin.content.labels', 'Upload') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
