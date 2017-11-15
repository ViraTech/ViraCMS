<div class="row-fluid">
  <div class="span8"><h6><?= Yii::t('vira_editor', 'File Name') ?></h6></div>
  <div class="span2"><h6><?= Yii::t('vira_editor', 'Type') ?></h6></div>
  <div class="span2"><h6><?= Yii::t('vira_editor', 'File Size') ?></h6></div>
</div>
<?php $this->widget('bootstrap.widgets.TbListView', array(
  'id'            => 'select-file',
  'dataProvider'  => $model->search(),
  'template'      => (isset($header) ? $header : '') . "{items}{pager}",
  'itemView'      => ($viewFile = $this->getViewFile('list')) != false ? $viewFile : 'application.components.ViraEditor.views.browse.file.list',
  'htmlOptions'   => array(
    'style' => 'padding-top: 0;',
  ),
  'pagerCssClass' => 'pagination',
  'ajaxUrl'       => $this->createUrl(Yii::app()->editor->fileBrowserAction),
)); ?>
