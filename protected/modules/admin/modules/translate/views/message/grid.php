<?php

$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => $model->getGridID(),
  'type' => 'striped bordered condensed',
  'dataProvider' => $model->search(),
  'template' => "{items}{summary}{pager}",
  'summaryText' => Yii::t('admin.translate.labels', 'Total messages found: {count}'),
  'summaryCssClass' => 'summary pull-right',
  'filter' => $model,
  'selectableRows' => 2,
  'afterAjaxUpdate' => 'afterAjaxUpdate',
  'columns' => array(
    array(
      'class' => 'VCheckBoxColumn',
    ),
    array(
      'name' => 'module',
      'headerHtmlOptions' => array('width' => '100'),
    ),
    array(
      'name' => 'category',
      'headerHtmlOptions' => array('width' => '100'),
    ),
    array(
      'name' => 'languageID',
      'filter' => CHtml::listData(VLanguageHelper::getLanguages(), 'id', 'title'),
      'headerHtmlOptions' => array('width' => '100'),
      'value' => '$data->language ? $data->language->title : ""',
    ),
    array(
      'name' => 'translate',
      'type' => 'html',
      'value' => '"<small><span class=\"muted\">" . ($data->source ? $data->source->source : "???") . "&nbsp;&rarr;&nbsp;</span>" . $data->translate . "</small>"',
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
  ),
));
