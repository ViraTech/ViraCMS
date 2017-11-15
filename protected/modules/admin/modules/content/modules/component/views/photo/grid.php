<?php

$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => $model->getGridID(),
  'type' => 'striped bordered condensed',
  'dataProvider' => $model->search(),
  'template' => "{items}{pager}",
  'selectableRows' => 2,
  'ajaxUrl' => $this->createUrl('index'),
  'afterAjaxUpdate' => 'afterAjaxUpdate',
  'htmlOptions' => array('style' => 'padding-top: 0'),
  'columns' => array(
    array(
      'class' => 'VCheckBoxColumn',
    ),
    array(
      'name' => 'title',
      'type' => 'raw',
      'value' => 'Yii::app()->getController()->renderPartial("grid-title",array("data" => $data))',
    ),
    array(
      'name' => 'siteID',
      'headerHtmlOptions' => array('width' => '150'),
      'type' => 'raw',
      'value' => 'CHtml::tag("div",array("style" => "max-width:150px;overflow:hidden;text-overflow:ellipsis;"),$data->site ? $data->site->title : Yii::t("admin.content.labels","Any Site"))',
    ),
    array(
      'class' => 'VButtonColumn',
      'size' => 'small',
      'actions' => array(
        array(
          'label' => Yii::t('common', 'View'),
          'url' => 'Yii::app()->getController()->createUrl("view",array("id" => $data->id))',
          'icon' => 'icon-eye-open',
        ),
        array(
          'type' => 'primary',
          'label' => Yii::t('common', 'Update'),
          'url' => 'Yii::app()->getController()->createUrl("update",array("id" => $data->id))',
          'icon' => 'icon-pencil',
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
