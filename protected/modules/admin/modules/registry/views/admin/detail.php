<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'id'),
    array('name' => 'roleID', 'value' => $model->role ? $model->role->title : $model->roleID),
    array('name' => 'status', 'value' => Yii::app()->collection->accountType->getAccountStatus(VAccountTypeCollection::ADMINISTRATOR, $model->status)),
    array('name' => 'username'),
    array('name' => 'email'),
    array('name' => 'name'),
  ),
));
