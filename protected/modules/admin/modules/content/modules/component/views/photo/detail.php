<?php

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data' => $model,
  'attributes' => array(
    array('name' => 'id'),
    array('name' => 'siteID', 'value' => $model->site ? $model->site->title : ''),
    array('name' => 'languageID', 'value' => VLanguageHelper::getLanguageTitle($model->languageID)),
    array('name' => 'title'),
    array('name' => 'public', 'type' => 'boolean'),
  ),
));
