<?php

$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => $model->getGridID(),
  'type' => 'striped bordered condensed',
  'dataProvider' => $model->search(),
  'template' => "{items}{pager}",
  'selectableRows' => 0,
  'ajaxUrl' => $this->createUrl('index'),
  'afterAjaxUpdate' => 'afterAjaxUpdate',
  'columns' => array(
    array(
      'name' => '_title',
      'value' => '$data->getTitle()',
    ),
    array(
      'name' => '_mcv',
      'value' => '$data->mcv ? Yii::t($data->mcv->translate,$data->mcv->title) : ""',
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
          'type' => 'primary',
          'label' => Yii::t('common', 'Update'),
          'url' => 'Yii::app()->getController()->createUrl("update",array("id" => $data->id))',
          'icon' => 'pencil',
        ),
        array(
          'type' => 'warning',
          'label' => Yii::t('common', 'Configure'),
          'url' => 'Yii::app()->getController()->createUrl("config",array("id" => $data->id))',
          'icon' => 'cog',
        ),
        array(
          'type' => 'danger',
          'label' => Yii::t('common', 'Delete'),
          'url' => 'Yii::app()->getController()->createUrl("delete",array("id" => $data->id))',
          'icon' => 'trash',
          'visible' => '$data->siteID',
        ),
      ),
      'headerHtmlOptions' => array('width' => '100'),
    ),
  ),
));
