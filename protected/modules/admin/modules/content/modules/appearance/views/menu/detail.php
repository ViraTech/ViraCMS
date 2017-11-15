<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'id'),
    array('name' => 'siteID', 'value' => $model->site ? $model->site->title : $model->siteID),
    array('name' => 'title'),
  ),
));
