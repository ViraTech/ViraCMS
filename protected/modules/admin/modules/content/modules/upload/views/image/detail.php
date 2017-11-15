<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'id'),
    array('name' => 'filename'),
    array('name' => 'mime'),
    array('name' => 'size', 'type' => 'size'),
    array('name' => 'path'),
    array('name' => 'comment'),
  ),
));
