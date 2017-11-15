<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'inline',
)); ?>
<fieldset>
  <legend><?= $legend ?></legend>
  <?= $form->errorSummary($model) ?>
  <?= $form->textFieldRow($model, 'email', array('class' => 'span3')) ?>
  <?= $form->captchaRow($model, 'captcha', array(
    'class' => 'span2',
    'captchaOptions' => array(
      'captchaAction' => '/admin/captcha/index',
    ),
  )) ?>
  <?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'label' => 'OK',
    'htmlOptions' => array('class' => 'span1 pull-right'),
  )); ?>
</fieldset>
<?php $this->endWidget(); ?>
