<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'vertical',
)); ?>
<?= $form->errorSummary($model) ?>
<?= $form->hiddenField($model, 'id') ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderPartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="general">
    <?= $form->uneditableRow($model, 'siteID', array('class' => 'input-block-level', 'value' => $model->site ? $model->site->title : $model->siteID)) ?>
    <?= $form->uneditableRow($model, 'title', array('class' => 'input-block-level')) ?>
  </div>
  <div class="tab-pane fade" id="menu-items">
    <?php $this->renderPartial('menu', array(
      'model' => $model,
      'view' => true,
    )); ?>
  </div>
  <div class="tab-pane fade" id="history">
    <fieldset>
      <?php $this->widget('application.widgets.core.VHistoryWidget', array(
        'model' => $model,
        'form' => $form,
      )); ?>
    </fieldset>
  </div>
</div>
<div class="form-actions">
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
