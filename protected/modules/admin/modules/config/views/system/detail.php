<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'id'),
    array('name' => 'siteID', 'value' => $model->site ? $model->site->title : ''),
    array('name' => '_title'),
    array('name' => '_mcv', 'value' => $model->mcv ? $model->mcv->getTitle() : $model->_mcv),
    array('name' => 'timeUpdated', 'type' => 'datetime'),
    array('name' => 'updatedBy', 'value' => $model->whoUpdated ? $model->whoUpdated->name : ''),
  ),
));
