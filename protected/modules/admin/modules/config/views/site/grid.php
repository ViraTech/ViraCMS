<?php

$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => $model->getGridID(),
  'type' => 'striped bordered condensed',
  'dataProvider' => $model->search(),
  'template' => "{items}{pager}",
  'filter' => $model,
  'selectableRows' => 2,
  'afterAjaxUpdate' => 'afterAjaxUpdate',
  'columns' => array(
    array(
      'class' => 'VCheckBoxColumn',
    ),
    array(
      'header' => Yii::t('admin.content.labels', 'Site Title'),
      'name' => 'title',
      'type' => 'raw',
      'value' => 'CHtml::tag("div",array("style" => "max-width:250px;overflow:hidden;text-overflow:ellipsis;"),$data->title)',
    ),
    array(
      'name' => 'default',
      'header' => Yii::t('admin.content.labels', 'Default Site'),
      'type' => 'boolean',
      'headerHtmlOptions' => array('width' => '100'),
      'filter' => Yii::app()->format->booleanFormat,
    ),
    array(
      'name' => 'host',
    ),
    array(
      'name' => 'domains',
      'type' => 'ntext',
    ),
    array(
      'name' => 'redirect',
      'header' => Yii::t('admin.content.labels', 'Redirect'),
      'type' => 'boolean',
      'headerHtmlOptions' => array('width' => '80'),
      'filter' => Yii::app()->format->booleanFormat,
    ),
    array(
      'class' => 'VButtonColumn',
      'filter' => $this->widget('application.widgets.core.VPageSizeWidget', array(
        'type' => 'button',
        'value' => Yii::app()->request->getParam('pageSize', Yii::app()->params['defaultPageSize']),
        ), true),
      'size' => 'small',
      'actions' => array(
        array(
          'type' => 'default',
          'label' => Yii::t('common', 'View'),
          'url' => 'Yii::app()->getController()->createUrl("view",array("id" => $data->id))',
          'icon' => 'eye-open',
        ),
        array(
          'type' => 'primary',
          'label' => Yii::t('common', 'Update'),
          'url' => 'Yii::app()->getController()->createUrl("update",array("id" => $data->id))',
          'icon' => 'pencil',
        ),
        array(
          'type' => 'danger',
          'label' => Yii::t('common', 'Delete'),
          'url' => 'Yii::app()->getController()->createUrl("delete",array("id" => $data->id))',
          'icon' => 'trash',
        ),
      ),
      'headerHtmlOptions' => array('width' => '100'),
    ),
  ),
));
