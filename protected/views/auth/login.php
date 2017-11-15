<h2><?= Yii::t('admin.registry.titles', 'Login Into Site') ?></h2>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'horizontal',
  )); ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <?= $form->textFieldRow($model, 'email', array('class' => 'span4')) ?>
  <?= $form->passwordFieldRow($model, 'password', array('class' => 'span4')) ?>
  <?= $form->checkboxRow($model, 'remember') ?>
  <?php if ($model->enableCaptcha): ?>
    <?= $form->captchaRow($model, 'captcha', array(
      'captchaOptions' => array(
        'captchaAction' => '/captcha/index',
        'showRefreshButton' => false,
      ),
    )) ?>
<?php endif; ?>
</fieldset>
<div class="form-actions">
  <button type="submit" class="btn btn-primary"><i class="icon-ok"></i> <?= Yii::t('common', 'Login') ?></button>
  <a href="<?= $this->createUrl('restore') ?>" class="btn btn-link"><?= Yii::t('admin.registry.labels', 'Restore Forgotten Password') ?></a>
</div>
<?php $this->endWidget(); ?>
