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
    <div class="control-group">
      <label class="control-label"><?= $model->getAttributeLabel('template') ?></label>
      <div class="controls">
        <?php $this->widget('application.extensions.ace.EAce', array(
          'model' => $model,
          'attribute' => 'template',
          'readonly' => true,
          'htmlOptions' => array(
            'style' => 'width: 100%; height: 300px; border: 1px solid #cccccc;',
          ),
        )); ?>
      </div>
    </div>
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
