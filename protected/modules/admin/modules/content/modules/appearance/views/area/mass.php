<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= $confirmation ?></legend>
  <?php $this->widget('bootstrap.widgets.TbAlert'); ?>
  <input type="hidden" name="action" value="<?= $action ?>" />
  <input type="hidden" name="list" value="<?= $list ?>" />
  <?php $this->widget('bootstrap.widgets.TbDetailView', array(
    'data' => $selected,
    'attributes' => $attributes,
  )); ?>
  <div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', $button); ?>
    <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
  </div>
</fieldset>
<?php $this->endWidget(); ?>
