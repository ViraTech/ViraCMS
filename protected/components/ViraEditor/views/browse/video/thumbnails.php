<?php $header = $view == 'list' ? '
<div class="row-fluid">
  <div class="span3"><strong><small>' . Yii::t('vira_editor','Thumbnail') . '</small></strong></div>
  <div class="span5"><strong><small>' . Yii::t('vira_editor','Media Object Name') . '</small></strong></div>
  <div class="span4"><strong><small>' . Yii::t('vira_editor','File Size') . '</small></strong></div>
</div>' : ''; ?>
<?php $this->widget('bootstrap.widgets.TbThumbnails', array(
  'id' => 'select-video',
  'dataProvider' => $model->search(),
  'template' => (isset($header) ? $header : '') . "{items}{pager}",
  'itemView' => ($viewFile = $this->getViewFile($view)) != false ? $viewFile : ('application.components.ViraEditor.views.browse.video.' . $view),
  'htmlOptions' => array(
    'style' => 'padding-top: 0;',
  ),
  'listCssClass' => $view == 'list' ? 'unstyled' : 'thumbnails',
  'ajaxUrl' => $this->createUrl(Yii::app()->editor->videoBrowserAction),
)); ?>
