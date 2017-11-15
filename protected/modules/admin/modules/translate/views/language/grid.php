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
      'headerHtmlOptions' => array('width' => '50'),
    ),
    array(
      'name' => 'active',
      'headerHtmlOptions' => array('width' => '100'),
      'type' => 'boolean',
      'filter' => Yii::app()->format->booleanFormat,
    ),
    array(
      'name' => 'locale',
      'headerHtmlOptions' => array('width' => '100'),
    ),
    array(
      'name' => 'title',
    ),
    array(
      'name' => 'index',
      'headerHtmlOptions' => array('width' => '90'),
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
          'url' => 'Yii::app()->getController()->createUrl("view", array("id" => $data->id))',
          'icon' => 'eye-open',
        ),
        array(
          'type' => 'primary',
          'label' => Yii::t('common', 'Update'),
          'url' => 'Yii::app()->getController()->createUrl("update", array("id" => $data->id))',
          'icon' => 'pencil',
        ),
        array(
          'type' => 'inverse',
          'label' => Yii::t('common', 'Disable'),
          'url' => 'Yii::app()->getController()->createUrl("disable", array("id" => $data->id))',
          'icon' => 'ban-circle',
          'visible' => '$data->active',
        ),
        array(
          'type' => 'success',
          'label' => Yii::t('common', 'Enable'),
          'url' => 'Yii::app()->getController()->createUrl("enable", array("id" => $data->id))',
          'icon' => 'ok-circle',
          'visible' => '!$data->active',
        ),
        array(
          'type' => 'danger',
          'label' => Yii::t('common', 'Delete'),
          'url' => 'Yii::app()->getController()->createUrl("delete", array("id" => $data->id))',
          'icon' => 'trash',
        ),
      ),
      'headerHtmlOptions' => array('width' => '100'),
    ),
  ),
));
