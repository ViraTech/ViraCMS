<?php $this->renderPartial('header'); ?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id' => $model->getGridID(),
  'type' => 'vertical',
)); ?>
<?= $form->errorSummary($model) ?>
<fieldset>
  <legend><?= $this->getTitle('view', array('model' => $model)) ?></legend>
</fieldset>
<?php $this->renderPartial('tabs'); ?>
<div class="tab-content">
  <div class="tab-pane fade active in" id="info">
    <div class="row-fluid">
      <div class="span3">
        <?= $form->uneditableRow($model, 'id', array('class' => 'input-block-level', 'hint' => $model->system ? Yii::t('admin.registry.labels', 'System (uneditable) Role') : '')) ?>
      </div>
      <div class="span9">
        <?= $form->uneditableRow($model, 'title', array('class' => 'input-block-level')) ?>
      </div>
    </div>
  </div>
  <div class="tab-pane fade" id="rules">
    <?= $form->checkBoxRow($model, 'allowAll', array('disabled' => 'disabled', 'labelOptions' => array('class' => 'muted'))) ?>
    <div class="row-fluid">
      <div class="span3">
        <ul class="nav nav-tabs nav-stacked">
        <?php foreach ($accessSections as $sectionID => $sectionData): ?>
          <li<?= $sectionID == 'core' ? ' class="active"' : '' ?>><a data-toggle="tab" href="#section-<?= $sectionID ?>"><?= Yii::t($sectionData['translate'], $sectionData['title']) ?></a></li>
        <?php endforeach; ?>
        </ul>
      </div>
      <div class="span9">
        <div class="tab-content">
        <?php foreach ($accessSections as $sectionID => $sectionData): ?>
          <div class="tab-pane fade<?= $sectionID == 'core' ? ' active in' : '' ?>" id="section-<?= $sectionID ?>" style="padding: 0; background: transparent;">
          <?php foreach ($accessGroups as $groupID => $groupData): ?>
            <?php if ($groupData['section'] == $sectionID): ?>
              <div class="well">
                <div class="nav-header"><?= Yii::t($groupData['translate'], $groupData['title']) ?></div>
                <div class="row-fluid">
                  <?php $c = 0; ?>
                  <?php foreach ($accessRules as $ruleID => $ruleData): ?>
                    <?php if ($ruleData['group'] == $groupID): ?>
                      <?php $name = 'accessFlags[' . $ruleID . ']'; ?>
                        <div class="span3">
                          <label class="checkbox">
                            <input type="checkbox" disabled="disabled"<?= $model->allowAll || isset($model->accessFlags[$ruleID]) && $model->accessFlags[$ruleID] ? ' checked' : '' ?>> <span><?= Yii::t($ruleData['translate'], $ruleData['title']) ?></span>
                          </label>
                        </div>
                        <?php if (++$c > 3): ?>
                </div>
                <div class="row-fluid">
                          <?php $c = 0; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
        </div>
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
<?php Yii::app()->getClientScript()->registerCss('localCss', '
.well {
  padding-top: 15px;
  adding-bottom: 10px;
}
input[type=checkbox] {
  margin-right: 10px;
}
input[type=checkbox] + span {
  display: block;
}
input[type=checkbox][disabled] + span {
  color: #999;
}
'); ?>
