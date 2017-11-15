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
<ul class="nav nav-tabs">
  <li class="active"><a href="#account" data-toggle="tab"><?= Yii::t('admin.registry.titles', 'Account') ?></a></li>
  <li><a href="#access" data-toggle="tab"><?= Yii::t('admin.registry.titles', 'Site Access') ?></a></li>
  <li><a href="#password" data-toggle="tab"><?= Yii::t('admin.registry.titles', 'Password') ?></a></li>
  <li><a href="#history" data-toggle="tab"><?= Yii::t('admin.registry.titles', 'History') ?></a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane fade active in" id="account">
    <div class="row-fluid">
      <div class="span6">
        <?= $form->dropDownListRow($model, 'roleID', CHtml::listData(VAccountRole::model()->findAll(), 'id', 'title'), array('class' => 'input-block-level')) ?>
      </div>
      <div class="span6">
        <?= $form->dropDownListRow($model, 'status', Yii::app()->collection->accountType->getAccountStatus(VAccountTypeCollection::ADMINISTRATOR), array('class' => 'input-block-level')) ?>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span6">
        <?= $form->textFieldRow($model, 'username', array('class' => 'input-block-level')) ?>
      </div>
      <div class="span6">
        <?= $form->textFieldRow($model, 'email', array('class' => 'input-block-level')) ?>
      </div>
    </div>
    <?= $form->textFieldRow($model, 'name', array('class' => 'input-block-level')) ?>
  </div>
  <div class="tab-pane fade" id="access">
    <?php if (Yii::app()->user->getAttribute('siteAccess') == 0): ?>
      <input type="hidden" nam="<?= CHtml::activeName($model, 'siteAccess') ?>" value="0" />
      <?php $this->renderPartial('sites', array(
        'model' => $model,
        'sites' => $sites,
      )); ?>
    <?php else: ?>
      <?= $form->checkBoxRow($model, 'siteAccess') ?>
      <div id="siteAccessList" style="display: <?= $model->siteAccess ? 'none' : 'block' ?>">
        <?php $this->renderPartial('sites', array(
          'model' => $model,
          'sites' => $sites,
        )); ?>
      </div>
    <?php endif; ?>
  </div>
  <div class="tab-pane fade" id="password">
    <?= $form->passwordFieldRow($model, 'newPassword', array('class' => 'span3')) ?>
    <?= $form->passwordFieldRow($model, 'newPasswordConfirm', array('class' => 'span3')) ?>
  </div>
  <div class="tab-pane fade" id="history">
    <?php $this->widget('application.widgets.core.VHistoryWidget', array(
      'model' => $model,
      'form' => $form,
    )); ?>
  </div>
</div>
<div class="form-actions">
  <button class="btn btn-primary" type="submit" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
  <a class="btn btn-link" href="<?= $this->createUrl('index') ?>"><?= Yii::t('common', 'Cancel') ?></a>
</div>
<?php $this->endWidget(); ?>
<?php $this->cs->registerScript('acllowAccessHandler', "
$('#" . CHtml::activeId($model, 'siteAccess') . "').on('click',function(e) {
  if ($(this).prop('checked')) {
    $('#siteAccessList').slideUp('fast');
  }
  else {
    $('#siteAccessList').slideDown('fast');
  }
});
"); ?>
