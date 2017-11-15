<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'vertical',
)); ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
  <?php $this->renderPartial('tabs'); ?>
  <div class="tab-content">
    <div class="tab-pane fade in active" id="content">
      <?= $form->uneditableRow($model, 'title', array('class' => 'input-block-level')) ?>
      <?= $form->uneditableRow($model, 'content', array('class' => 'input-block-level uneditable-textarea', 'style' => 'height: 300px; overflow-y: scroll;')) ?>
    </div>
    <div class="tab-pane fade" id="style">
      <span class="input-block-level uneditable-textarea" style="height: 300px; overflow-y: hidden;"><?= CHtml::encode($model->style) ?></span>
    </div>
    <div class="tab-pane fade" id="script">
      <span class="input-block-level uneditable-textarea" style="height: 300px; overflow-y: hidden;"><?= CHtml::encode($model->script) ?></span>
    </div>
    <div class="tab-pane fade" id="history">
      <?php $this->widget('application.widgets.core.VHistoryWidget', array(
        'model' => $model,
        'form' => $form,
      )); ?>
    </div>
  </div>
</fieldset>
<div class="form-actions">
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
