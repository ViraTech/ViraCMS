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
      'name' => 'id',
      'headerHtmlOptions' => array('width' => '100'),
    ),
    array(
      'name' => 'title',
    ),
    array(
      'name' => 'siteID',
      'headerHtmlOptions' => array('width' => '200'),
      'filter' => CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'),
      'value' => '$data->site ? $data->site->shortTitle : $data->siteID',
    ),
    array(
      'name' => 'default',
      'type' => 'boolean',
      'filter' => Yii::app()->format->booleanFormat,
      'header' => Yii::t('admin.content.labels', 'Default'),
      'headerHtmlOptions' => array('width' => '100'),
    ),
    array(
      'type' => 'html',
      'header' => Yii::t('admin.content.titles', 'Page Areas'),
      'value' => 'Yii::app()->controller->renderPartial("areas",array("model" => $data),true,false)',
      'headerHtmlOptions' => array('width' => '200'),
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
          'type' => 'primary',
          'label' => Yii::t('common', 'Update'),
          'url' => 'Yii::app()->getController()->createUrl("update",array("id" => implode(",",$data->getPrimaryKey())))',
          'icon' => 'pencil',
        ),
        array(
          'type' => 'warning',
          'label' => Yii::t('admin.content.labels', 'Configure'),
          'url' => 'Yii::app()->getController()->createUrl("config",array("id" => implode(",",$data->getPrimaryKey())))',
          'icon' => 'cog',
        ),
        array(
          'type' => 'danger',
          'label' => Yii::t('common', 'Delete'),
          'url' => 'Yii::app()->getController()->createUrl("delete",array("id" => implode(",",$data->getPrimaryKey())))',
          'icon' => 'trash',
        ),
      ),
      'headerHtmlOptions' => array('width' => '100'),
    ),
  ),
));
