<?php $this->renderPartial('header'); ?>
<?php $this->widget('bootstrap.widgets.TbAlert'); ?>
<div class="row">
  <div class="span4">
    <div class="well">
      <h4><?= Yii::t('common', 'Filter') ?></h4>
      <form method="get" class="filter" id="form-filter">
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By Site') ?></div>
        <div class="filter-select">
          <?= CHtml::activeDropDownList($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'shortTitle'), array('empty' => '', 'class' => 'input-block-level')) ?>
        </div>
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By Page Title') ?></div>
        <div class="filter-text">
          <?= CHtml::activeTextField($model, '_title', array('class' => 'input-block-level')) ?>
        </div>
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By Page URL') ?></div>
        <div class="filter-text">
          <?= CHtml::activeTextField($model, 'url', array('class' => 'input-block-level')) ?>
        </div>
        <div class="nav-header"><?= Yii::t('common', 'Page Size') ?></div>
        <div class="filter-select">
          <?php $this->widget('application.widgets.core.VPageSizeWidget', array(
            'type' => 'select',
            'value' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
            'htmlOptions' => array(
              'class' => 'input-block-level',
            )
          )); ?>
        </div>
        <div class="filter-actions">
          <button type="submit" class="btn btn-info"><i class="icon-filter"></i> <?= Yii::t('common', 'Apply') ?></button>
          <a href="<?= $this->createUrl('index') ?>" class="btn btn-link" id="form-filter-reset"><?= Yii::t('common', 'Reset') ?></a>
        </div>
      </form>
    </div>
    <a href="<?= $this->createUrl('create') ?>" class="btn btn-success btn-large btn-block"><i class="icon-plus-sign"></i> <?= Yii::t('admin.content.labels', 'Add Page') ?></a>
  </div>
  <div class="span8">
    <?php $this->renderPartial('grid', array('model' => $model)); ?>
  </div>
</div>

<?php $this->cs->registerScript('reloadHandler', "
var filterHandleTimer;
var filterHandle = function(e) {
  e.preventDefault();
  clearTimeout(filterHandleTimer);
  filterHandleTimer = setTimeout(function()
  {
    $.fn.yiiGridView.update('" . $model->getGridID() . "',{
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

<?php $this->cs->registerScript(get_class($model) . '_ReadOnlyCells', "
$('.grid-view tr[data-non-removable]').unbind('click').click(function(e){e.stopPropagation();});"); ?>
<?php $this->cs->registerScript(get_class($model) . '_GridHandlers', "
function afterAjaxUpdate(target){ $('#' + target + ' tr[data-non-removable]').click(function(e){e.stopPropagation();}); }
function onSelectionChange(target){ $('#' + target + ' tr[data-non-removable]').removeClass('selected'); }
", CClientScript::POS_END); ?>
<?php $this->cs->registerScript(get_class($model) . '_ProcessSelected', "function processSelected() { $('input[name^=" . $model->getGridID() . "_c0]:checked').clone().hide().prependTo('#mass'); return true; }", CClientScript::POS_HEAD); ?>
