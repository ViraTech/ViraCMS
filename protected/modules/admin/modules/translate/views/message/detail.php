<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'id'),
    array('name' => 'module'),
    array('name' => 'category'),
    array('name' => 'source.source'),
    array('name' => 'translate'),
  ),
));
