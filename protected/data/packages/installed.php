<?php

return array(
  array(
    'id' => 'vira.core.core',
    'type' => 'core',
    'language' => 'ru-RU',
    'version' => '1.0.0',
    'uninstallable' => false,
    'upgradable' => true,
    'depend' => array(
      'vira.core.yii' => '1.1.14',
    ),
  ),
  array(
    'id' => 'vira.core.yii',
    'type' => 'core',
    'language' => 'en-US',
    'version' => '1.1.14',
    'uninstallable' => false,
    'upgradable' => true,
    'depend' => array(),
  ),
  array(
    'id' => 'vira.core.coredefbase',
    'type' => 'core',
    'language' => 'ru-RU',
    'version' => '1.0.0',
    'uninstallable' => false,
    'upgradable' => false,
    'depend' => array(
      'vira.core.core' => '1.0.0',
    ),
  ),
);
