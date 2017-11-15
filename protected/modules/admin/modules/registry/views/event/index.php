<?php $this->renderPartial('header'); ?>
<?php $this->widget('bootstrap.widgets.TbAlert'); ?>
<div class="row">
  <div class="span4">
    <div class="well">
      <h4><?= Yii::t('common', 'Filter') ?></h4>
      <form method="get" class="filter" id="form-filter">
        <input type="hidden" id="page-size" name="pageSize" value="<?= Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']) ?>" />
        <div class="nav-header"><?= Yii::t('admin.registry.filter', 'By Site') ?></div>
        <div class="filter-select">
          <?= CHtml::activeDropDownList($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'shortTitle'), array('empty' => '', 'class' => 'input-block-level')) ?>
        </div>
        <div class="nav-header"><?= Yii::t('admin.registry.filter', 'By Account ID') ?></div>
        <div class="filter-text">
          <input type="text" name="<?= CHtml::activeName($model, 'authorID') ?>" class="input-block-level" value="<?= $model->authorID ?>" />
        </div>
        <div class="nav-header"><?= Yii::t('admin.registry.filter', 'By Date Range') ?></div>
        <div class="filter-range">
          <div class="row-fluid">
            <div class="span6">
              <?= CHtml::activeHiddenField($model, 'time[start]') ?>
              <label class="range"><h6><small class="muted"><?= Yii::t('common', 'Start Date') ?></small></h6><input type="text" class="input-block-level" rel="<?= empty($model->time['start']) ? '' : $model->time['start'] ?>" data-datepicker="datepicker" data-format="<?= Yii::app()->format->dateFormat ?>" data-source="<?= CHtml::activeId($model, 'time[start]') ?>" value="<?= empty($model->time['start']) ? '' : Yii::app()->format->formatDate($model->time['start']) ?>" /></label>
            </div>
            <div class="span6">
              <?= CHtml::activeHiddenField($model, 'time[end]') ?>
              <label class="range"><h6><small class="muted"><?= Yii::t('common', 'End Date') ?></small></h6><input type="text" class="input-block-level" rel="<?= empty($model->time['end']) ? '' : $model->time['end'] ?>" data-datepicker="datepicker" data-format="<?= Yii::app()->format->dateFormat ?>" data-source="<?= CHtml::activeId($model, 'time[end]') ?>" value="<?= empty($model->time['end']) ? '' : Yii::app()->format->formatDate($model->time['end']) ?>" /></label>
            </div>
          </div>
        </div>
        <div class="nav-header"><?= Yii::t('admin.registry.filter', 'By IP Address') ?></div>
        <div class="filter-text">
          <input type="text" name="<?= CHtml::activeName($model, 'remote') ?>" class="input-block-level" value="<?= $model->remote ?>" />
        </div>
        <div class="nav-header"><?= Yii::t('common', 'Page Size') ?></div>
        <div class="filter-select">
          <?php $this->widget('application.widgets.core.VPageSizeWidget', array(
            'type' => 'select',
            'value' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
            'htmlOptions' => array(
              'class' => 'input-block-level',
            ),
          )); ?>
        </div>
        <div class="filter-actions">
          <button type="submit" class="btn btn-info"><i class="icon-filter"></i> <?= Yii::t('common', 'Apply') ?></button>
          <a href="<?= $this->createUrl('index') ?>" class="btn btn-link" id="form-filter-reset"><?= Yii::t('common', 'Reset') ?></a>
        </div>
      </form>
    </div>
    <div class="well">
      <h4><?= Yii::t('admin.registry.titles', 'Log Info') ?></h4>
      <div class="log-info">
        <div class="nav-header"><?= Yii::t('common', 'Number of Entries') ?></div>
        <p><?= $info['entriesQty'] ?></p>
        <div class="nav-header"><?= Yii::t('common', 'Log Started') ?></div>
        <p><?= $info['minDatetime'] ? Yii::app()->format->formatDatetime($info['minDatetime']) : '&mdash;' ?></p>
        <div class="nav-header"><?= Yii::t('common', 'Last Log Entry') ?></div>
        <p><?= $info['maxDatetime'] ? Yii::app()->format->formatDatetime($info['maxDatetime']) : '&mdash;' ?></p>
      </div>
    </div>
    <div class="well">
      <h4><?= Yii::t('common', 'Actions') ?></h4>
      <div class="log-actions">
        <a href="<?= $this->createUrl('clear') ?>" class="btn btn-danger"><i class="icon-trash"></i> <?= Yii::t('admin.registry.labels', 'Clear Log') ?></a>
        <a href="<?= $this->createUrl('download') ?>" class="btn btn-success"><i class="icon-download-alt"></i> <?= Yii::t('common', 'Download') ?></a>
      </div>
    </div>
  </div>
  <div class="span8">
    <div class="filtered-content">
      <div class="row-fluid log-header">
        <div class="span3"><h6><?= Yii::t('admin.registry.labels', 'Site') ?></h6></div>
        <div class="span3"><h6><?= Yii::t('admin.registry.labels', 'Account') ?></h6></div>
        <div class="span6"><h6><?= Yii::t('admin.registry.labels', 'Event') ?></h6></div>
      </div>
      <?php $this->renderPartial('grid', array(
        'model' => $model,
      )); ?>
    </div>
  </div>
</div>
<?php $this->cs->registerScript('reloadHandler', "
var filterHandleTimer;
var filterHandle = function(e) {
  e.preventDefault();
  clearTimeout(filterHandleTimer);
  filterHandleTimer = setTimeout(function() {
    $.fn.yiiListView.update('" . $model->getGridID() . "', {
      data: $('#form-filter').serialize()
    });
  },100);
};
$('#form-filter').on('submit',filterHandle);
$('select,input','#form-filter').on('change',filterHandle);
$('#form-filter-reset').click(function(e) {
  $('.filter-text input,.filter-select select,.filter-range input','#form-filter').val('').attr('rel','');
  filterHandle(e);
});
"); ?>
<?php Yii::app()->bootstrap->registerDatepickerJS(); ?>
