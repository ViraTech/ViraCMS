<?php
/**
 * ViraCMS Console Application Command Map
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
return array(
  'language' => 'en',
  'import' => array(
    'core.commands.*',
  ),
  'commandMap' => array(
    'core' => array(
      'class' => 'core.commands.VCoreCommand',
    ),
    'cache' => array(
      'class' => 'core.commands.VCacheCommand',
    ),
    'migrate' => array(
      'class' => 'core.commands.VMigrationCommand',
    ),
    'search' => array(
      'class' => 'core.commands.VSearchCommand',
    ),
    'user' => array(
      'class' => 'core.commands.VUserCommand',
    ),
  ),
);
