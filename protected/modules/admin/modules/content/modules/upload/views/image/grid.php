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
      'name' => 'filename',
      'header' => Yii::t('admin.content.labels', 'Preview'),
      'type' => 'raw',
      'value' => 'CHtml::link(CHtml::image($data->getUrl(65,65,1),$data->filename,array("width" => 65,"height" => 65)),Yii::app()->storage->getFileUrl($data->path),array("target" => "_blank"))',
      'headerHtmlOptions' => array('width' => '65'),
      'filter' => false,
    ),
    array(
      'name' => 'filename',
      'type' => 'html',
      'value' => 'CHtml::tag("div",array("style" => "max-width:300px;overflow:hidden;text-overflow:ellipsis;"),$data->filename) . ($data->className ? "<br><small class=\"muted\">" . $data->className . "[" . $data->primaryKey . "]" . "</small>" : "") . CHtml::link(CHtml::tag("i",array("class" => "icon-external-link"),""),$data->getUrl(),array("class" => "muted pull-right","target" => "_blank"))',
    ),
    array(
      'name' => 'size',
      'type' => 'html',
      'value' => 'Yii::app()->format->formatSize($data->size) . "<br>" . CHtml::tag("small",array("class" => "muted"),$data->width . "&times;" . $data->height)',
      'headerHtmlOptions' => array('width' => '100'),
    ),
    array(
      'name' => 'siteID',
      'value' => '$data->site ? $data->site->shortTitle : Yii::t("admin.content.labels","Any Site")',
      'filter' => CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'shortTitle'),
    ),
    array(
      'name' => 'comment',
      'headerHtmlOptions' => array('width' => '150'),
      'type' => 'raw',
      'value' => '"<small class\"muted\">" . $data->comment . "</small>"',
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
          'label' => Yii::t('common', 'View'),
          'url' => 'Yii::app()->getController()->createUrl("view",array("id" => $data->getPrimaryKey()))',
          'icon' => 'eye-open',
          'type' => 'default',
        ),
        array(
          'label' => Yii::t('common', 'Download'),
          'url' => 'Yii::app()->getController()->createUrl("download",array("id" => $data->getPrimaryKey()))',
          'icon' => 'download',
          'type' => 'inverse',
        ),
        array(
          'label' => Yii::t('common', 'Update'),
          'url' => 'Yii::app()->getController()->createUrl("update",array("id" => $data->getPrimaryKey()))',
          'icon' => 'pencil',
          'type' => 'primary',
        ),
        array(
          'label' => Yii::t('common', 'Delete'),
          'url' => 'Yii::app()->getController()->createUrl("delete",array("id" => $data->getPrimaryKey()))',
          'icon' => 'trash',
          'type' => 'danger',
        ),
      ),
      'headerHtmlOptions' => array('width' => '100'),
    ),
  ),
));
