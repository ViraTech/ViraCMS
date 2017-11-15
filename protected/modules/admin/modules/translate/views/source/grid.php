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
  'enableHistory' => true,
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
      'name' => 'source',
      'value' => 'mb_strlen($data->source) > VTranslateSource::MESSAGE_CUT ? mb_strcut($data->source, 0, VTranslateSource::MESSAGE_CUT, Yii::app()->charset) . "..." : $data->source',
    ),
    array(
      'header' => Yii::t('admin.translate.titles', 'Has Translation'),
      'type' => 'raw',
      'filter' => '<div style="margin:6px 0 0 0;"><span class="label label-success">' . Yii::app()->format->formatBoolean(1) . '</span>&nbsp;<span class="label label-important">' . Yii::app()->format->formatBoolean(0) . '</span></div>',
      'value' => 'Yii::app()->controller->renderTranslationButtons($data)',
      'headerHtmlOptions' => array('width' => '250'),
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
