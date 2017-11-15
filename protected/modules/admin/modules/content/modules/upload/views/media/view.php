<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'horizontal',
)); ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
  <?= $form->uneditableRow($model, 'siteID', array('class' => 'input-block-level', 'value' => $model->site ? $model->site->title : $model->siteID)) ?>
  <?= $form->uneditableRow($model, 'filename', array('class' => 'input-block-level')) ?>
  <?= $form->uneditableRow($model, 'url', array('class' => 'input-block-level', 'value' => $model->getUrl())) ?>
  <?= $form->uneditableRow($model, 'mime', array('class' => 'input-block-level')) ?>
  <?= $form->uneditableRow($model, 'size') ?>
  <?php if (($player = $model->getPlayer()) !== false): ?>
  <div class="control-group">
    <div class="controls">
      <?= $player->getCode($model) ?>
    </div>
  </div>
  <?php endif; ?>
  <?= $form->uneditableRow($model, 'comment', array('class' => 'input-block-level uneditable-textarea')) ?>
</fieldset>
<div class="form-actions">
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
