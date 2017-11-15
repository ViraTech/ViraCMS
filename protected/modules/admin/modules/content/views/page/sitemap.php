<?php

$this->widget($alias, array(
  'id' => 'sitemap',
  'items' => Yii::app()->siteMap->get($site->id),
));
