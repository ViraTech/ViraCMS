<?php

$this->widget('bootstrap.widgets.TbListView', array(
  'dataProvider' => $model->search(),
  'itemView' => 'entry',
  'emptyText' => CHtml::tag('span', array('class' => 'muted'), Yii::t('admin.registry.messages', 'No log entries found.')),
  'template' => '{items}{pager}',
  'ajaxUrl' => $this->createUrl('index'),
  'id' => $model->getGridID(),
  'enableHistory' => false,
));
