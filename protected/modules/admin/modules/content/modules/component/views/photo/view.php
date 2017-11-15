<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'vertical',
)); ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderpartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="common">
    <fieldset>
      <?= $form->uneditableRow($model, 'siteID', array(
        'class' => 'input-block-level',
        'value' => $model->site ? $model->site->title : '',
      )) ?>
      <div class="row-fluid">
        <div class="span10">
          <?= $form->uneditableRow($model, 'title', array('class' => 'input-block-level')) ?>
        </div>
        <div class="span2">
          <?= $form->uneditableRow($model, 'public', array(
            'class' => 'input-block-level',
            'type' => 'boolean',
          )) ?>
        </div>
      </div>
    </fieldset>
  </div>
  <div class="tab-pane fade" id="images">
    <div class="row-fluid">
      <ul class="thumbnails">
      <?php foreach ($this->getImages($model) as $image): ?>
        <li class="span3 thumbnail">
          <img src="<?= call_user_func_array(array($image->image, 'getUrl'), array(PhotoController::IMAGE_PREVIEW_WIDTH, PhotoController::IMAGE_PREVIEW_HEIGHT, 1)) ?>" />
          <div class="caption"><?= $image->title ? $image->title : '&mdash;' ?></div>
        </li>
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="tab-pane fade" id="history">
    <fieldset>
    <?php $this->widget('application.widgets.core.VHistoryWidget', array(
      'model' => $model,
      'form' => $form,
    )); ?>
    </fieldset>
  </div>
</div>
<div class="form-actions">
  <a class="btn btn-default" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
  <a class="btn btn-primary" href="<?= $this->createUrl('update', array('id' => $model->id)) ?>"><i class="icon-pencil"></i> <?= Yii::t('common', 'Update') ?></a>
</div>
<?php $this->endWidget(); ?>
<?php $this->cs->registerCss('Thumbnails', "
.thumbnail .caption {
  height: 40px;
  overflow: hidden;
  padding: 0;
  margin: 9px;
}
"); ?>
