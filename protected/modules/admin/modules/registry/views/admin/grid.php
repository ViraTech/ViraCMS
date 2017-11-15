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
      'name' => 'name',
      'type' => 'html',
      'value' => '$data->name . "<br><small class=\"muted\">" . $data->username . "<br>" . $data->email . "</small>"',
    ),
    array(
      'name' => 'roleID',
      'filter' => CHtml::listData(VAccountRole::model()->findAll(), 'id', 'title'),
      'value' => '$data->role ? $data->role->title : $data->roleID',
    ),
    array(
      'name' => 'status',
      'filter' => Yii::app()->collection->accountType->getAccountStatus(VAccountTypeCollection::ADMINISTRATOR),
      'headerHtmlOptions' => array('width' => '95'),
      'value' => 'Yii::app()->collection->accountType->getAccountStatus(VAccountTypeCollection::ADMINISTRATOR, $data->status)',
    ),
    array(
      'name' => 'siteAccess',
      'header' => Yii::t('admin.registry.labels', 'Site Access'),
      'headerHtmlOptions' => array('width' => '150'),
      'type' => 'raw',
      'value' => '$data->siteAccess ? Yii::t("admin.registry.labels", "Any Site") : implode(", ", CHtml::listData($data->sites, "id", "shortTitle"))',
      'filter' => CHtml::listData(VSite::model()->autoFilter()->findAll(), 'id', 'title'),
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
          'type' => 'success',
          'label' => Yii::t('common', 'Enable'),
          'url' => 'Yii::app()->getController()->createUrl("enable", array("id" => $data->id))',
          'icon' => 'ok-circle',
          'visible' => '$data->status != VAccountTypeCollection::STATUS_ADMINISTRATOR_ACTIVE',
        ),
        array(
          'type' => 'inverse',
          'label' => Yii::t('common', 'Disable'),
          'url' => 'Yii::app()->getController()->createUrl("disable", array("id" => $data->id))',
          'icon' => 'ban-circle',
          'visible' => '$data->status == VAccountTypeCollection::STATUS_ADMINISTRATOR_ACTIVE',
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
