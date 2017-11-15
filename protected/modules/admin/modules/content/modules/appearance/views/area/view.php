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
    <?= $form->uneditableRow($model, 'title', array('class' => 'input-block-level')) ?>
    <div class="row-fluid">
      <div class="span10">
        <?= $form->uneditableRow($model, 'type', array('class' => 'input-block-level', 'value' => Yii::app()->collection->pageAreaType->itemAt($model->type))) ?>
      </div>
      <div class="span2">
        <?= $form->uneditableRow($model, 'position', array('class' => 'input-block-level')) ?>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span6">
        <?= $form->uneditableRow($model, 'tag', array('class' => 'input-block-level')) ?>
      </div>
      <div class="span6">
        <?= $form->uneditableRow($model, 'container', array('class' => 'input-block-level', 'value' => Yii::app()->collection->pageAreaContainer->itemAt($model->container))) ?>
      </div>
    </div>
    <?= $form->uneditableRow($model, 'classes', array('class' => 'input-block-level')) ?>
  </div>
  <div class="tab-pane fade" id="layouts">
  <?php foreach ($layouts as $siteID => $list): ?>
    <div class="control-group">
      <label class="control-label"><?= $sites[$siteID] ?></label>
      <div class="controls">
      <?php $layouts = $model->getLayouts(); ?>
      <?php foreach ($list as $layoutID => $title): ?>
        <label class="checkbox inline disabled"><input type="checkbox" disabled value="<?= $title ?>"<?= isset($layouts[$siteID][$layoutID]) ? ' checked' : '' ?>> <?= $title ?></label>
      <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
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
