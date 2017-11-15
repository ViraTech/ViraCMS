<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type' => 'vertical',
  )); ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
</fieldset>
<ul class="nav nav-tabs">
  <li class="active"><a href="#account" data-toggle="tab"><?= Yii::t('admin.registry.titles', 'Account') ?></a></li>
  <li><a href="#access" data-toggle="tab"><?= Yii::t('admin.registry.titles', 'Site Access') ?></a></li>
  <li><a href="#history" data-toggle="tab"><?= Yii::t('admin.registry.titles', 'History') ?></a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane fade active in" id="account">
    <div class="row-fluid">
      <div class="span6">
        <?= $form->uneditableRow($model, 'roleID', array('class' => 'input-block-level', 'value' => $model->role ? $model->role->title : $model->roleID)) ?>
      </div>
      <div class="span6">
        <?= $form->uneditableRow($model, 'status', array('class' => 'input-block-level', 'value' => Yii::app()->collection->accountType->getAccountStatus(VAccountTypeCollection::ADMINISTRATOR, $model->status))) ?>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span6">
        <?= $form->uneditableRow($model, 'username', array('class' => 'input-block-level')) ?>
      </div>
      <div class="span6">
        <?= $form->uneditableRow($model, 'email', array('class' => 'input-block-level')) ?>
      </div>
    </div>
    <?= $form->uneditableRow($model, 'name', array('class' => 'input-block-level')) ?>
  </div>
  <div class="tab-pane fade" id="access">
    <div class="control-group">
      <label class="control-label"><?= Yii::t('admin.registry.labels', 'Allow Access To Sites') ?></label>
      <div class="controls">
        <span class="uneditable-input uneditable-textarea input-block-level">
        <?php if ($model->siteAccess): ?>
          <?= Yii::t('admin.registry.labels', 'Any Site') ?>
        <?php else: ?>
          <?php $access = array(); ?>
          <?php foreach ($sites as $i => $site): ?>
            <?php if ($model->hasSiteAccess($site->id)): ?>
              <?php $access[] = $site->title; ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?= implode(', ', $access) ?>
        <?php endif; ?>
        </span>
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
<div class="form-actions">
  <a class="btn" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'OK') ?></a>
</div>
<?php $this->endWidget(); ?>
