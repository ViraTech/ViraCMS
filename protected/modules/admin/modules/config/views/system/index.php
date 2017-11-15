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
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By System View') ?></div>
        <div class="filter-select">
          <?= CHtml::activeDropDownList($model, '_mcv', $this->getMcvList(), array('empty' => '', 'class' => 'input-block-level')) ?>
        </div>
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By Create/Update Time') ?></div>
        <div class="filter-range">
          <div class="row-fluid">
            <div class="span6">
              <?= CHtml::activeHiddenField($model, 'timeUpdated[start]') ?>
              <label class="range">
                <h6><small class="muted"><?= Yii::t('common', 'Start Date') ?></small></h6>
                <input type="text" class="input-block-level" data-datepicker="datepicker"
                       rel="<?= empty($model->timeUpdated['start']) ? '' : $model->timeUpdated['start'] ?>"
                       data-format="<?= Yii::app()->format->dateFormat ?>"
                       data-source="<?= CHtml::activeId($model, 'timeUpdated[start]') ?>"
                       value="<?= empty($model->timeUpdated['start']) ? '' : Yii::app()->format->formatDate($model->timeUpdated['start']) ?>"
                       />
              </label>
            </div>
            <div class="span6">
              <?= CHtml::activeHiddenField($model, 'timeUpdated[end]') ?>
              <label class="range">
                <h6><small class="muted"><?= Yii::t('common', 'End Date') ?></small></h6>
                <input type="text" class="input-block-level" data-datepicker="datepicker"
                       rel="<?= empty($model->timeUpdated['end']) ? '' : $model->timeUpdated['end'] ?>"
                       data-format="<?= Yii::app()->format->dateFormat ?>"
                       data-source="<?= CHtml::activeId($model, 'timeUpdated[end]') ?>"
                       value="<?= empty($model->timeUpdated['end']) ? '' : Yii::app()->format->formatDate($model->timeUpdated['end']) ?>"
                       />
              </label>
            </div>
          </div>
        </div>
        <div class="nav-header"><?= Yii::t('common', 'Page Size') ?></div>
        <div class="filter-select">
          <?php
          $this->widget('application.widgets.core.VPageSizeWidget', array(
            'type' => 'select',
            'value' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
            'htmlOptions' => array(
              'class' => 'input-block-level',
            )
          ));
          ?>
        </div>
        <div class="filter-actions">
          <button type="submit" class="btn btn-info"><i class="icon-filter"></i> <?= Yii::t('common', 'Apply') ?></button>
          <a href="<?= $this->createUrl('index') ?>" class="btn btn-link" id="form-filter-reset"><?= Yii::t('common', 'Reset') ?></a>
        </div>
      </form>
    </div>
    <div class="well">
      <h4><?= Yii::t('common', 'Actions') ?></h4>
      <div class="log-actions">
        <a href="<?= $this->createUrl('create') ?>" class="btn btn-success"><i class="icon-plus-sign"></i> <?= Yii::t('admin.content.labels', 'Create System Page') ?></a>
      </div>
    </div>
  </div>
  <div class="span8">
<?php $this->renderPartial('grid', array('model' => $model)); ?>
  </div>
</div>

<?php $this->cs->registerScript('reloadHandler', "
var filterHandleTimer,
	filterHandle = function(e) {
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

<?php $this->cs->registerScript(get_class($model) . '_AfterAjaxUpdate', "function afterAjaxUpdate(){}", CClientScript::POS_HEAD); ?>
<?php $this->cs->registerScript(get_class($model) . '_ProcessSelected', "function processSelected() { $('input[name^=" . $model->getGridID() . "_c0]:checked').clone().hide().prependTo('#mass'); return true; }", CClientScript::POS_HEAD); ?>
