<h3><?= Yii::t('admin.registry.titles', 'Password Restore') ?></h3>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'horizontal',
  )); ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <?= $form->textFieldRow($model, 'email') ?>
  <?= $form->captchaRow($model, 'captcha', array(
    'captchaOptions' => array(
      'captchaAction' => '/captcha/index',
      'showRefreshButton' => false,
    ),
  )) ?>
</fieldset>
<div class="form-actions">
  <button type="submit" class="btn btn-primary"><?= Yii::t('admin.registry.labels', 'Restore') ?></button>
</div>
<?php $this->endWidget(); ?>
