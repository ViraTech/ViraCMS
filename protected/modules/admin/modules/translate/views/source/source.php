<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'horizontal',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
  <?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= Yii::t('admin.translate.titles', 'Download Source Messages') ?></legend>
  <?= $form->dropDownListRow($model, 'languageID', $languages, array(
    'empty' => '',
    'hint' => Yii::t('admin.translate.hints', '(optional)'),
    'onchange' => "if ($(this).val()) { $('#checkbox-bl').slideDown('fast'); } else { $('#checkbox-bl').slideUp('fast'); }",
  )) ?>
  <?= $form->dropDownListRow($model, 'encoding', array_combine($model->getAvailableEncodings(), $model->getAvailableEncodings())) ?>
  <div style="display: <?= $model->languageID ? 'block' : 'none' ?>;" id="checkbox-bl">
    <?= $form->checkBoxRow($model, 'withoutTranslation') ?>
  </div>
</fieldset>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('admin.translate.labels', 'Generating...')) ?>"><i class="icon-download-alt"></i> <?= Yii::t('common', 'Download') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
