<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'vertical',
)); ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderPartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="general">
    <?= $form->uneditableRow($model, 'title', array('class' => 'input-block-level')) ?>
    <?= $form->uneditableRow($model, 'host', array('class' => 'input-block-level')) ?>
    <?= $form->uneditableRow($model, 'domains', array('class' => 'input-block-level uneditable-textarea')) ?>
    <?= $form->uneditableRow($model, 'redirect', array('type' => 'boolean')) ?>
    <?= $form->uneditableRow($model, 'theme', array('class' => 'input-block-level', 'value' => isset($themes[$model->theme]) ? $themes[$model->theme] : '???')) ?>
    <?= $form->uneditableRow($model, 'webroot', array('class' => 'input-block-level')) ?>
    <?= $form->checkBoxRow($model, 'default', array('disabled' => 'disabled', 'labelOptions' => array('disabled' => 'disabled', 'class' => 'disabled'))) ?>
  </div>
  <div class="tab-pane fade" id="history">
    <?php $this->widget('application.widgets.core.VHistoryWidget', array(
      'model' => $model,
      'form' => $form,
    )); ?>
  </div>
</div>
<div class="form-actions">
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
