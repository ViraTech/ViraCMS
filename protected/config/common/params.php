<?php
/**
 * Default Application Parameters
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
return array(
  'params' => array(
    'adminEmail' => 'root@localhost.localdomain',
    'advancedSettings' => false,
    'defaultPageSize' => 10,
    'passwordLengthMin' => 6,
    'passwordLengthMax' => 21,
    'defaultCacheDuration' => 60,
    'defaultCacheTagDuration' => 3600,
    'allowImageTypes' => 'jpg,jpeg,png,bmp,gif,tif,tiff',
    'allowAudioTypes' => 'mp3,ogg,wav',
    'allowVideoTypes' => 'mp4,flv,webp',
    'allowFlashTypes' => 'swf',
    'stayLoggedIn' => 3600,
    'passwordRestoreTTL' => 86400,
  ),
);
