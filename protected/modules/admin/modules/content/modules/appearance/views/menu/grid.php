<?php

$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => $model->getGridID(),
  'type' => 'striped bordered condensed',
  'dataProvider' => $model->search(),
  'template' => "{items}{pager}",
  'filter' => $model,
  'selectableRows' => 2,
  'afterAjaxUpdate' => 'afterAjaxUpdate',
  'columns' => array_filter(array(
    array(
      'class' => 'VCheckBoxColumn',
    ),
    array(
      'name' => 'title',
    ),
    Yii::app()->user->getAttribute('siteID') == 0 ? array(
      'name' => 'siteID',
      'value' => '$data->site ? $data->site->title : $data->siteID',
      'filter' => CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'),
      'headerHtmlOptions' => array('width' => '300'),
      ) : null,
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
          'type' => 'danger',
          'label' => Yii::t('common', 'Delete'),
          'url' => 'Yii::app()->getController()->createUrl("delete", array("id" => $data->id))',
          'icon' => 'trash',
        ),
      ),
      'headerHtmlOptions' => array('width' => '100'),
    ),
  )),
));
