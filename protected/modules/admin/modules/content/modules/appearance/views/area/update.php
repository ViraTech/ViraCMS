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
    <?= $form->textFieldRow($model, 'title', array('class' => 'input-block-level')) ?>
    <div class="row-fluid">
      <div class="span10">
      <?php if ($model->type == VPageAreaTypeCollection::COMMON): ?>
        <?= $form->uneditableRow($model, 'type', array('class' => 'input-block-level', 'value' => Yii::app()->collection->pageAreaType->itemAt($model->type))) ?>
      <?php else: ?>
        <?= $form->dropDownListRow($model, 'type', $pageAreaTypes, array('class' => 'input-block-level')) ?>
      <?php endif; ?>
      </div>
      <div class="span2">
        <?= $form->textFieldRow($model, 'position', array('class' => 'input-block-level')) ?>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span6">
        <?= $form->textFieldRow($model, 'tag', array('class' => 'input-block-level')) ?>
      </div>
      <div class="span6">
        <?= $form->dropDownListRow($model, 'container', Yii::app()->collection->pageAreaContainer->toArray(), array('class' => 'input-block-level')) ?>
      </div>
    </div>
    <?= $form->textFieldRow($model, 'classes', array('class' => 'input-block-level')) ?>
  </div>
  <div class="tab-pane fade" id="layouts">
  <?php foreach ($layouts as $siteID => $list): ?>
    <div class="control-group">
      <label class="control-label"><?= $sites[$siteID] ?></label>
      <div class="controls">
      <?php $layouts = $model->getLayouts(); ?>
      <?php foreach ($list as $layoutID => $title): ?>
        <label class="checkbox inline"><input type="checkbox" name="layouts[<?= $siteID ?>][<?= $layoutID ?>]" value="<?= $title ?>"<?= isset($layouts[$siteID][$layoutID]) ? ' checked' : '' ?>> <?= $title ?></label>
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
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
