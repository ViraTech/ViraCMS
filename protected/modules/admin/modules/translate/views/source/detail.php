<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'module'),
    array('name' => 'category'),
    array('name' => 'source'),
  ),
));
