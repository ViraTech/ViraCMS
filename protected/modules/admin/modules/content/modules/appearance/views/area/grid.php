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
      'name' => 'title',
      'headerHtmlOptions' => array('width' => '200'),
    ),
    array(
      'type' => 'html',
      'header' => Yii::t('admin.content.titles', 'Used In Site Layouts'),
      'value' => 'Yii::app()->getController()->renderPartial("shown",array("model" => $data),true,false)',
    ),
    array(
      'name' => 'type',
      'headerHtmlOptions' => array('width' => '150'),
      'filter' => Yii::app()->collection->pageAreaType->toArray(),
      'value' => 'Yii::app()->collection->pageAreaType->itemAt($data->type)',
    ),
    array(
      'name' => 'position',
      'headerHtmlOptions' => array('width' => '50'),
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
