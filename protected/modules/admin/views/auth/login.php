<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'inline',
)); ?>
<fieldset>
  <legend><?= $legend ?></legend>
  <?= $form->errorSummary($model) ?>
  <?= $form->textFieldRow($model, 'username', array('class' => 'input-block-level')) ?>
  <?php if ($model->enableCaptcha): ?>
    <div class="clearfix">
      <?php $this->widget('CCaptcha', array(
        'captchaAction' => '/admin/captcha/index',
        'showRefreshButton' => false,
        'imageOptions' => array('class' => 'pull-right'),
      )); ?>
    <?= $form->textFieldRow($model, 'captcha', array('class' => 'input-small')) ?>
    </div>
  <?php endif; ?>
  <?= $form->passwordFieldRow($model, 'password', array('class' => 'span2')) ?>
  <?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'label' => Yii::t('common', 'Login'),
    'htmlOptions' => array('class' => 'pull-right'),
  )); ?>
  <div>
    <a class="forgot" href="<?= $this->createUrl('restore') ?>"><?= Yii::t('admin.registry.labels', 'Forgot Password?') ?></a>
    <div class="btn-group pull-right">
      <a href="#" class="btn btn-info btn-small dropdown-toggle" data-toggle="dropdown"><?= VLanguageHelper::getLanguageTitle() ?> <span class="caret"></span></a>
      <ul class="dropdown-menu">
      <?php foreach (VLanguageHelper::getLanguages() as $language): ?>
        <li><a href="?lang=<?= $language->id ?>"><?= $language->title ?></a></li>
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
</fieldset>
<?php $this->endWidget(); ?>
