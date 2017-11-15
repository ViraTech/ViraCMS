<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'id'),
    array('name' => 'title'),
    array('name' => 'type', 'value' => Yii::app()->collection->pageAreaType->itemAt($model->type)),
    array('name' => 'tag'),
    array('name' => 'container'),
    array('name' => 'classes'),
    array('name' => 'position'),
  ),
));
