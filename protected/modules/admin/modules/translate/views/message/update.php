<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => $model->getGridID(),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<?= $form->hiddenField($model, 'hash') ?>
<?= $form->hiddenField($model, 'languageID') ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
  <div class="row-fluid">
    <div class="span6">
      <?= $form->uneditableRow($model, 'module', array('class' => 'input-block-level')) ?>
    </div>
    <div class="span6">
  <?= $form->uneditableRow($model, 'category', array('class' => 'input-block-level')) ?>
    </div>
  </div>
  <?= $form->uneditableRow($model->source, 'source', array('class' => 'input-block-level')) ?>
  <?= $form->uneditableRow($model, 'languageID', array('class' => 'input-block-level', 'value' => VLanguageHelper::getLanguageTitle($model->languageID))) ?>
  <?= $form->textFieldRow($model, 'translate', array('class' => 'input-block-level')) ?>
</fieldset>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= urldecode(Yii::app()->request->getParam('return', $this->createUrl('index'))) ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
