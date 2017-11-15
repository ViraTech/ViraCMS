<div class="well" style="padding: 10px 20px 0;">
  <div class="nav-header" style="padding-left: 0;"><?= Yii::t('admin.titles', 'Engine Configuration') ?></div>
  <div class="control-group">
    <label class="control-label"><?= Yii::t('admin.labels', 'ViraCMS Version') ?></label>
    <div class="controls">
      <div class="row-fluid">
        <div class="span3">
          <span class="uneditable-input input-block-level"><?= Yii::app()->getVersion() ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label"><?= Yii::t('admin.labels', 'Application Core Path') ?></label>
    <div class="controls">
      <span class="uneditable-textarea input-block-level" style="min-height: 60px; word-wrap: break-word;"><?= $model->getCorePath() ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label"><?= Yii::t('admin.labels', 'Constants File Writeable') ?></label>
    <div class="controls">
      <span class="uneditable-input span1"><?= Yii::app()->format->formatBoolean($model->isWriteable('const')) ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label"><?= Yii::t('admin.labels', 'Local Override File Writeable') ?></label>
    <div class="controls">
      <span class="uneditable-input span1"><?= Yii::app()->format->formatBoolean($model->isWriteable('local')) ?></span>
    </div>
  </div>
</div>

<div class="well" style="padding: 10px 20px 0;">
  <div class="nav-header" style="padding-left: 0;"><?= Yii::t('admin.titles', 'Site Configuration') ?></div>
  <div class="control-group">
    <label class="control-label"><?= Yii::t('admin.labels', 'Enable Maintenance Mode') ?></label>
    <div class="controls">
      <label class="radio inline">
        <input type="radio" name="config[maintenance]" id="maintenance_1" value="1"<?= Yii::app()->maintenance ? ' checked="checked"' : '' ?>><?= Yii::t('common', 'Yes') ?>
      </label>
      <label class="radio inline">
        <input type="radio" name="config[maintenance]" id="maintenance_0" value="0"<?= Yii::app()->maintenance ? '' : ' checked="checked"' ?>><?= Yii::t('common', 'No') ?>
      </label>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label"><?= Yii::t('admin.labels', 'Site Mode') ?></label>
    <div class="controls">
      <label class="radio inline">
        <input type="radio" name="mode" id="site_mode_0" value="development"<?= $model->mode == 'development' ? ' checked="checked"' : '' ?>><?= Yii::t('admin.labels', 'Development Mode') ?>
      </label>
      <label class="radio inline">
        <input type="radio" name="mode" id="site_mode_1" value="production"<?= $model->mode == 'development' ? '' : ' checked="checked"' ?>><?= Yii::t('admin.labels', 'Production Mode') ?>
      </label>
    </div>
  </div>
</div>
