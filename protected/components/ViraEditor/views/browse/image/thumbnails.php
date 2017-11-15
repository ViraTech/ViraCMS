<?php $header = $view == 'list' ? '
<div class="row-fluid">
  <div class="span2"><strong><small>' . Yii::t('vira_editor','Thumbnail') . '</small></strong></div>
  <div class="span5"><strong><small>' . Yii::t('vira_editor','Image Name') . '</small></strong></div>
  <div class="span3"><strong><small>' . Yii::t('vira_editor','Image Size') . '</small></strong></div>
  <div class="span2"><strong><small>' . Yii::t('vira_editor','Dimensions') . '</small></strong></div>
</div>' : $header = ''; ?>
<?php $this->widget('bootstrap.widgets.TbThumbnails', array(
  'id' => 'select-image',
  'dataProvider' => $model->search(),
  'emptyText' => Yii::t('vira_editor','No images uploaded yet.'),
  'template' => (isset($header) ? $header : '') . "{items}{pager}",
  'itemView' => ($viewFile = $this->getViewFile($view)) != false ? $viewFile : ('application.components.ViraEditor.views.browse.image.' . $view),
  'htmlOptions' => array(
    'style' => 'padding-top: 0;',
  ),
  'listCssClass' => $view == 'list' ? 'unstyled' : 'thumbnails',
  'pagerCssClass' => 'pagination',
  'ajaxUrl' => $this->createUrl(Yii::app()->editor->imageBrowserAction),
)); ?>
