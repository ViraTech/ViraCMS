<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => 'form-' . strtolower(get_class($model)),
  'type' => 'vertical',
  'htmlOptions' => array(
    'onsubmit' => "$('button[type=submit]',this).button('loading'); return true;",
  ),
)); ?>
<h2 class="page-header"><?= Yii::t('admin.titles', 'My Profile') ?></h2>
<div class="row-fluid">
  <div class="span3">
    <?php $this->widget('bootstrap.widgets.TbMenu', array(
      'type' => 'tabs',
      'stacked' => true,
      'items' => array(
        array(
          'label' => Yii::t('admin.titles', 'Identification'),
          'url' => '#ident',
          'linkOptions' => array('data-toggle' => 'tab'),
          'active' => true,
        ),
        array(
          'label' => Yii::t('admin.titles', 'Account'),
          'url' => '#account',
          'linkOptions' => array('data-toggle' => 'tab'),
        ),
        array(
          'label' => Yii::t('admin.titles', 'Change Password'),
          'url' => '#auth',
          'linkOptions' => array('data-toggle' => 'tab'),
        ),
        array(
          'label' => Yii::t('admin.titles', 'Security'),
          'url' => '#secure',
          'linkOptions' => array('data-toggle' => 'tab'),
        ),
      ),
    )); ?>
    <div class="form-actions">
      <button class="btn btn-success" type="submit" name="send" data-loading-text="<?= CHtml::encode('<i class="icon-spinner icon-spin"></i> ' . Yii::t('common', 'Saving...')) ?>"><i class="icon-ok"></i> <?= Yii::t('common', 'Save') ?></button>
      <a href="<?= $this->createUrl('/admin/default/index') ?>" class="btn btn-link"><?= Yii::t('common', 'Cancel') ?></a>
    </div>
  </div>
  <div class="span9">
    <div class="row-alert">
      <?php $this->widget('bootstrap.widgets.TbAlert'); ?>
    </div>
    <?= $form->errorSummary($model) ?>
    <div class="tab-content">
      <div class="tab-pane fade active in" id="ident" style="background: transparent; padding: 0;">
        <div class="well">
          <fieldset>
            <legend><?= Yii::t('admin.titles', 'Identification') ?></legend>
            <div class="row-fluid">
              <div class="span6">
                <?= $form->textFieldRow($model, 'username', array('class' => 'input-block-level')) ?>
              </div>
              <div class="span6">
                <?= $form->textFieldRow($model, 'email', array('class' => 'input-block-level')) ?>
              </div>
            </div>
                <?= $form->textFieldRow($model, 'name', array('class' => 'input-block-level')) ?>
          </fieldset>
        </div>
      </div>
      <div class="tab-pane fade" id="account" style="background: transparent; padding: 0;">
        <div class="well">
          <fieldset>
            <legend><?= Yii::t('admin.titles', 'Account') ?></legend>
            <div class="row-fluid">
              <div class="span4">
                <?= $form->uneditableRow($model, 'status', array('value' => Yii::app()->collection->accountType->getAccountStatus(VAccountTypeCollection::ADMINISTRATOR, $model->status), 'class' => 'input-block-level')) ?>
              </div>
              <div class="span4">
                <?= $form->uneditableRow($model, 'roleID', array('value' => $model->role ? $model->role->title : $model->roleID, 'class' => 'input-block-level')) ?>
              </div>
              <div class="span4">
                <?= $form->dropDownListRow($model, 'languageID', CHtml::listData(VLanguageHelper::getLanguages(), 'id', 'title'), array('empty' => '', 'class' => 'input-block-level')) ?>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
      <div class="tab-pane fade" id="auth" style="background: transparent; padding: 0;">
        <div class="well">
          <fieldset>
            <legend><?= Yii::t('admin.titles', 'Password') ?></legend>
            <?= $form->passwordFieldRow($model, 'newPassword') ?>
            <?= $form->passwordFieldRow($model, 'newPasswordConfirm') ?>
          </fieldset>
        </div>
      </div>
      <div class="tab-pane fade" id="secure" style="background: transparent; padding: 0;">
        <div class="well">
          <fieldset>
            <legend><?= Yii::t('admin.titles', 'Security') ?></legend>
            <?php $this->renderPartial('security', array(
              'account' => $model,
              'model' => new VLogAuth('search'),
            )); ?>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->endWidget(); ?>
