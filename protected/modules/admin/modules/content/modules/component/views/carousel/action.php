<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => get_class($model),
  'type' => 'horizontal',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= $confirmation ?></legend>
  <?php $this->renderPartial('detail', array(
    'model' => $model,
  )); ?>
</fieldset>
<?php if ($this->action->id == 'delete'): ?>
  <div class="alert alert-block">
    <h4><?= Yii::t('admin.content.messages', 'Warning!') ?></h4>
    <p><?= Yii::t('admin.content.messages', 'All of carousel images will be lost.') ?></p>
  </div>
<?php endif; ?>
<div class="form-actions">
  <?php $this->widget('bootstrap.widgets.TbButton', $button); ?>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
