<?php

$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => $model->getGridID(),
  'type' => 'striped bordered condensed',
  'dataProvider' => $model->search(),
  'template' => "{items}{pager}",
  'selectableRows' => 2,
  'afterAjaxUpdate' => 'afterAjaxUpdate',
  'selectionChanged' => 'onSelectionChange',
  'rowHtmlOptionsExpression' => '$data->homepage ? array("data-non-removable" => "true") : array()',
  'columns' => array_filter(array(
    array(
      'name' => 'currentL10n.name',
      'type' => 'raw',
      'value' => '$data->title . "<br><small class=\"muted\">" . $data->url . "</small>" . CHtml::link(CHtml::tag("i",array("class" => "icon-external-link"),""),$data->createUrl(true),array("class" => "muted pull-right","target" => "_blank"))',
    ),
    array(
      'name' => 'layoutID',
      'value' => '$data->layout ? $data->layout->title : $data->layoutID',
    ),
    Yii::app()->user->getAttribute('siteID') == 0 ? array(
      'name' => 'siteID',
      'value' => '$data->site ? $data->site->title : $data->siteID',
      'filter' => CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'),
      'headerHtmlOptions' => array('width' => '200'),
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
          'type' => 'success',
          'label' => Yii::t('admin.content.labels', 'Add Children Page'),
          'url' => 'Yii::app()->getController()->createUrl("create",array("parent" => $data->id))',
          'icon' => 'plus',
          'visible' => '!$data->homepage',
        ),
        array(
          'type' => 'danger',
          'label' => Yii::t('common', 'Delete'),
          'url' => 'Yii::app()->getController()->createUrl("delete",array("id" => $data->id))',
          'icon' => 'trash',
          'visible' => '!$data->homepage',
        ),
      ),
      'headerHtmlOptions' => array('width' => '100'),
    ),
  )),
));
