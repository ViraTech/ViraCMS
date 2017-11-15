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
    <?= $form->uneditableRow($model, 'module', array('class' => 'input-block-level')) ?>
    <?= $form->uneditableRow($model, 'name', array('class' => 'input-block-level')) ?>
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
            <?= $form->uneditableRow($l10n, 'subject', array('class' => 'input-block-level uneditable-textarea')) ?>
            <?= $form->uneditableRow($l10n, 'body', array('class' => 'input-block-level uneditable-textarea', 'type' => 'ntext')) ?>
            <?= $form->checkBoxRow($l10n, 'isHtml', array('disabled' => 'disabled', 'labelOptions' => array('class' => 'disabled'))); ?>
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
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
