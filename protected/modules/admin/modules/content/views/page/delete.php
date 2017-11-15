<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => get_class($model),
  'type' => 'horizontal',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
  <?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= Yii::t('admin.content.titles', 'Are you sure to delete site page?') ?></legend>
  <?php $this->widget('bootstrap.widgets.TbDetailView', array(
    'data' => $model,
    'attributes' => array(
      array('name' => 'id'),
      array('name' => 'currentL10n.name'),
      array('name' => 'siteID', 'value' => $model->site ? $model->site->name : '???'),
      array('name' => 'url'),
      array('name' => 'layoutID'),
    ),
  )); ?>
</fieldset>
<?php if ($model->children): ?>
  <div class="well">
    <h5><?= Yii::t('admin.content.labels', 'Page has children pages') ?></h5>
    <p>
      <label class="radio inline">
        <input type="radio" name="children" id="children1" value="<?= PageController::CONNECT_CHILDREN_TO_PARENT ?>" checked>
        <?= Yii::t('admin.content.labels', 'Connect children pages to parent page') ?>
      </label>
      <label class="radio inline">
        <input type="radio" name="children" id="children2" value="<?= PageController::DELETE_CHILDREN ?>">
        <?= Yii::t('admin.content.labels', 'Delete children pages') ?>
      </label>
    </p>
  </div>
  <?php endif; ?>
<div class="form-actions">
  <?php $this->widget('bootstrap.widgets.TbButton', array(
    'type' => 'danger',
    'buttonType' => 'submit',
    'label' => Yii::t('common', 'Delete'),
    'icon' => 'icon-trash',
    'htmlOptions' => array(
      'name' => 'delete',
      'data-loading-text' => '<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Removing...'),
    ),
  )); ?>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
