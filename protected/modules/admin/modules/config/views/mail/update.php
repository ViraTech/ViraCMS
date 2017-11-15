<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => $model->getGridID(),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
  )); ?>
<?= $form->errorSummary($model) ?>
<?= $form->hiddenField($model, 'id') ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderPartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="general">
    <?= $form->textFieldRow($model, 'module', array('class' => 'input-block-level')) ?>
    <?= $form->textFieldRow($model, 'name', array('class' => 'input-block-level')) ?>
  </div>
  <div class="tab-pane fade" id="content">
    <ul class="nav nav-tabs">
      <?php foreach ($languages as $language): ?>
        <li<?= $language->id == Yii::app()->getLanguage() ? ' class="active"' : '' ?>><a href="#title-language-<?= $language->id ?>" data-toggle="tab"><?= $language->title ?></a></li>
      <?php endforeach; ?>
    </ul>
    <div class="tab-content">
    <?php foreach ($languages as $language): ?>
        <div class="tab-pane fade<?= $language->id == Yii::app()->getLanguage() ? ' active in' : '' ?>" id="title-language-<?= $language->id ?>">
          <div style="padding: 0 20px 0 0;">
            <?php $l10n = $model->getL10nModel($language->id); ?>
            <?= $form->textFieldRow($l10n, 'subject', array(
              'class' => 'input-block-level',
              'id' => get_class($l10n) . '_' . $language->id . '_subject',
              'name' => get_class($l10n) . '[' . $language->id . '][subject]',
            )) ?>
            <?= $form->textAreaRow($l10n, 'body', array(
              'class' => 'input-block-level',
              'rows' => 10, 'id' => get_class($l10n) . '_' . $language->id . '_body',
              'name' => get_class($l10n) . '[' . $language->id . '][body]',
            )) ?>
            <?= $form->checkBoxRow($l10n, 'isHtml', array(
              'id' => get_class($l10n) . '_' . $language->id . '_isHtml',
              'name' => get_class($l10n) . '[' . $language->id . '][isHtml]',
            )); ?>
          </div>
        </div>
    <?php endforeach; ?>
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
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
