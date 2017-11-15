<?php $this->renderPartial('header'); ?>
<?php $this->widget('bootstrap.widgets.TbAlert'); ?>
<div class="row">
  <div class="span4">
    <p><a href="<?= $this->createUrl('create') ?>" class="btn btn-success btn-large btn-block"><i class="icon-plus-sign"></i> <?= Yii::t('admin.content.titles', 'Add Photo') ?></a></p>
    <div class="well">
      <h4><?= Yii::t('common', 'Filter') ?></h4>
      <form method="get" class="filter" id="form-filter">
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By Site') ?></div>
        <div class="filter-select">
          <?= CHtml::activeDropDownList($model, 'siteID', CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'shortTitle'), array('empty' => '', 'class' => 'input-block-level')) ?>
        </div>
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By Language') ?></div>
        <div class="filter-select">
          <?= CHtml::activeDropDownList($model, 'languageID', CHtml::listData(VLanguageHelper::getLanguages(), 'id', 'title'), array('empty' => '', 'class' => 'input-block-level')) ?>
        </div>
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By Published Flag') ?></div>
        <div class="filter-select">
          <?= CHtml::activeDropDownList($model, 'public', Yii::app()->format->booleanFormat, array('empty' => '', 'class' => 'input-block-level')) ?>
        </div>
        <div class="nav-header"><?= Yii::t('admin.content.titles', 'By Title') ?></div>
        <div class="filter-text">
          <?= CHtml::activeTextField($model, 'title', array('class' => 'input-block-level')) ?>
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
  </div>
  <div class="span8">
    <?php $this->renderPartial('grid', array('model' => $model)); ?>
    <form method="post" id="mass" class="mass-actions" onsubmit="return processSelected()">
      <?= Yii::t('common', 'Mass actions with selected:') ?>
      <div class="btn-group">
        <button name="delete" class="btn btn-danger" type="submit">
          <i class="icon-trash"></i> <?= Yii::t('common', 'Delete') ?>
        </button>
      </div>
    </form>
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
$('#pagesize-selector .dropdown-menu a').on('click',function(e) {
  var self = $(this);
  $('#pagesize-selector > a').html(self.text() + ' <span class=\"caret\"></span');
  $('#page-size').val(self.data('size'));
  filterHandle(e);
});
$('#form-filter-reset').click(function(e) {
  $('.filter-text input,.filter-select select,.filter-range input','#form-filter').val('').attr('rel','');
  filterHandle(e);
});
"); ?>
<?php Yii::app()->bootstrap->registerDatepickerJS(); ?>

<?php $this->cs->registerScript(get_class($model) . '_AfterAjaxUpdate', "function afterAjaxUpdate(){}", CClientScript::POS_HEAD); ?>
<?php $this->cs->registerScript(get_class($model) . '_ProcessSelected', "function processSelected() { $('input[name^=" . $model->getGridID() . "_c0]:checked').clone().hide().prependTo('#mass'); return true; }", CClientScript::POS_HEAD); ?>
