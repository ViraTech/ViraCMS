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
    <?= $form->uneditableRow($model, 'siteID', array('class' => 'input-block-level', 'value' => $model->site ? $model->site->title : '')) ?>
    <ul class="nav nav-tabs" id="languages">
    <?php foreach ($languages as $language): ?>
      <li<?= $language->id == Yii::app()->getLanguage() ? ' class="active"' : '' ?>>
        <a data-toggle="tab" href="#l10n-<?= $language->id ?>"><?= $language->title ?></a>
      </li>
    <?php endforeach; ?>
    </ul>
    <div class="tab-content">
    <?php foreach ($languages as $language): ?>
      <div class="tab-pane fade<?= $language->id == Yii::app()->getLanguage() ? ' active in' : '' ?>" id="l10n-<?= $language->id ?>">
        <?php $l10n = $model->getL10nModel($language->id, false); ?>
        <?= $form->uneditableRow($l10n, 'title', array('class' => 'input-block-level')) ?>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
  <div class="tab-pane fade" id="carousel">
    <div class="row-fluid">
    <?php foreach ($model->images as $image): ?>
    <?php $l10n = $image->getL10nModel(); ?>
      <div class="span4">
        <div class="thumbnail">
          <img src="<?= $image->image->getUrl(CarouselController::IMAGE_PREVIEW_WIDTH, CarouselController::IMAGE_PREVIEW_HEIGHT, true) ?>" alt="" />
          <div class="caption"><?= $l10n->title ?></div>
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
  <a class="btn btn-default" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
