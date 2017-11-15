<?php

$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => 'security-log-grid',
  'type' => 'striped bordered condensed',
  'dataProvider' => $model->log($account->id),
  'template' => "{items}{pager}",
  'columns' => array(
    array(
      'name' => 'siteID',
      'value' => '$data->site ? $data->site->title : $data->siteID',
      'headerHtmlOptions' => array('width' => '200'),
    ),
    array(
      'name' => 'type',
      'headerHtmlOptions' => array('width' => '150'),
      'filter' => Yii::app()->collection->authLogType->toArray(),
      'value' => 'Yii::app()->collection->authLogType->itemAt($data->type)',
    ),
    array(
      'header' => Yii::t('admin.titles', 'Success'),
      'name' => 'result',
      'type' => 'boolean',
      'headerHtmlOptions' => array('width' => '50'),
    ),
    array(
      'name' => 'time',
      'type' => 'datetime',
      'headerHtmlOptions' => array('width' => '100'),
    ),
    array(
      'name' => 'remote',
      'type' => 'ip4address',
      'headerHtmlOptions' => array('width' => '100'),
    ),
  ),
));
