<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => $model->getGridID(),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onSubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<?= $form->errorSummary($model) ?>
<?= $form->hiddenField($model, 'id') ?>
<fieldset>
  <legend><?= $this->getTitle($model->isNewRecord ? 'create' : 'update', array('model' => $model)) ?></legend>
  <?php $this->renderPartial('tabs'); ?>
  <div class="tab-content">
    <div class="tab-pane fade in active" id="content">
      <?= $form->textFieldRow($model, 'title', array('class' => 'input-block-level')) ?>
      <div class="control-group">
        <label class="control-label"><?= $model->getAttributeLabel('content') ?></label>
        <div class="controls">
          <?php $this->widget('application.extensions.ckeditor.ECKEditor', array(
            'model' => $model,
            'attribute' => 'content',
            'enableServerBrowsing' => true,
            'toolbar' => 'Full',
            'height' => 500,
            'serverBrowsingParams' => array(
              'className' => get_class($model),
              'primaryKey' => $model->id,
            ),
            'htmlOptions' => array(
              'class' => 'input-block-level',
              'rows' => 5,
            ),
          )); ?>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="style">
      <div class="control-group">
        <div class="controls">
          <?php $this->widget('application.extensions.ace.EAce', array(
            'model' => $model,
            'attribute' => 'style',
            'mode' => 'css',
            'htmlOptions' => array(
              'style' => 'width: 100%; height: 300px; border: 1px solid #cccccc;',
            ),
          )); ?>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="script">
      <div class="control-group">
        <div class="controls">
          <?php $this->widget('application.extensions.ace.EAce', array(
            'model' => $model,
            'attribute' => 'script',
            'mode' => 'javascript',
            'htmlOptions' => array(
              'style' => 'width: 100%; height: 300px; border: 1px solid #cccccc;',
            ),
          )); ?>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="history">
      <?php $this->widget('application.widgets.core.VHistoryWidget', array(
        'model' => $model,
        'form' => $form,
      )); ?>
    </div>
  </div>
</fieldset>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" name="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
