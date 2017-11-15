<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => $model->getGridID(),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onSubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<?= $form->hiddenField($model, 'id') ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderPartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="general">
    <?= $form->textFieldRow($model, 'title', array('class' => 'input-block-level')) ?>
    <?= $form->textFieldRow($model, 'host', array('class' => 'input-block-level')) ?>
    <?= $form->textAreaRow($model, 'domains', array('class' => 'input-block-level', 'rows' => 7, 'hint' => Yii::t('admin.content.messages', 'Every domain must be entered from the new line. You may not add the same domains for different sites.'))) ?>
    <?= $form->radioButtonListInlineRow($model, 'redirect', Yii::app()->format->booleanFormat) ?>
    <?= $form->dropDownListRow($model, 'theme', $themes, array('class' => 'input-block-level')) ?>
    <?= $form->textFieldRow($model, 'webroot', array('class' => 'input-block-level', 'hint' => Yii::t('admin.content.messages', 'Leave blank for autodetect.'))) ?>
    <?= $form->checkBoxRow($model, 'default', array('hint' => Yii::t('admin.content.messages', 'Check if you want to make this site default. Note that this mark will be cleared on other sites.'))) ?>
  </div>
  <div class="tab-pane fade" id="history">
    <?php $this->widget('application.widgets.core.VHistoryWidget', array(
      'model' => $model,
      'form' => $form,
    )); ?>
  </div>
</div>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
