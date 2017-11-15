<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'id'),
    array('name' => 'title'),
    array('name' => 'template', 'type' => 'ntext'),
  ),
));
