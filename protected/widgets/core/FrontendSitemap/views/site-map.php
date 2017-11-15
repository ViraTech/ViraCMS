<h2><?= Yii::t('common', 'Site Map') ?></h2>
<?php

$this->widget('zii.widgets.CMenu', array(
  'items' => Yii::app()->siteMap->getMenu(Yii::app()->site->id),
));
