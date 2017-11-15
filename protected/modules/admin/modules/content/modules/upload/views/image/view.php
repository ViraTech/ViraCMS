<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'horizontal',
)); ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
  <?= $form->uneditableRow($model, 'siteID', array('class' => 'input-block-level', 'value' => $model->site ? $model->site->title : $model->siteID)) ?>
  <?= $form->uneditableRow($model, 'filename', array('class' => 'input-block-level')) ?>
  <?= $form->uneditableRow($model, 'mime', array('class' => 'input-block-level')) ?>
  <?= $form->uneditableRow($model, 'size', array('value' => Yii::app()->format->formatSize($model->size) . ', ' . $model->width . '&times;' . $model->height)) ?>
  <div class="control-group">
    <div class="controls">
      <?= CHtml::image($model->getUrl(120, 0, 0, 0, 0), $model->filename, array("width" => 120)) ?>
    </div>
  </div>
<?= $form->uneditableRow($model, 'comment', array('class' => 'input-block-level uneditable-textarea')) ?>
</fieldset>
<div class="form-actions">
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
