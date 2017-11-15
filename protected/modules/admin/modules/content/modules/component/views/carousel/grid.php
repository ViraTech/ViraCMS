<?php

$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => $model->getGridID(),
  'type' => 'striped bordered condensed',
  'dataProvider' => $model->search(),
  'template' => "{items}{pager}",
  'selectableRows' => 2,
  'afterAjaxUpdate' => 'afterAjaxUpdate',
  'columns' => array(
    array(
      'class' => 'VCheckBoxColumn',
    ),
    array(
      'name' => '_title',
      'type' => 'raw',
      'value' => 'Yii::app()->getController()->renderPartial("grid-title",array("data" => $data))',
    ),
    array(
      'name' => 'siteID',
      'headerHtmlOptions' => array('width' => '150'),
      'type' => 'raw',
      'value' => 'CHtml::tag("div",array("style" => "max-width:150px;overflow:hidden;text-overflow:ellipsis;"),$data->site ? $data->site->title : $data->siteID)',
    ),
    array(
      'class' => 'VButtonColumn',
      'size' => 'small',
      'actions' => array(
        array(
          'label' => Yii::t('common', 'View'),
          'url' => 'Yii::app()->controller->createUrl("view",array("id" => $data->id))',
          'icon' => 'eye-open',
          'type' => 'default',
        ),
        array(
          'label' => Yii::t('common', 'Update'),
          'url' => 'Yii::app()->controller->createUrl("update",array("id" => $data->id))',
          'icon' => 'pencil',
          'type' => 'primary',
        ),
        array(
          'label' => Yii::t('common', 'Publish'),
          'url' => 'Yii::app()->controller->createUrl("enable",array("id" => $data->id))',
          'icon' => 'ok-circle',
          'type' => 'success',
        ),
        array(
          'label' => Yii::t('common', 'Hide'),
          'url' => 'Yii::app()->controller->createUrl("disable",array("id" => $data->id))',
          'icon' => 'ban-circle',
          'type' => 'inverse',
        ),
        array(
          'label' => Yii::t('common', 'Delete'),
          'url' => 'Yii::app()->controller->createUrl("delete",array("id" => $data->id))',
          'icon' => 'trash',
          'type' => 'danger',
        ),
      ),
      'headerHtmlOptions' => array('width' => '100'),
    ),
  ),
));
