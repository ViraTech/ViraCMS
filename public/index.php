<?php
/**
 * ViraCMS Bootstrap Script
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
// predefined constants if any
@include_once(implode(DIRECTORY_SEPARATOR, array(
  dirname(dirname(__FILE__)),
  'protected',
  'config',
  'const.php',
)));

// if no constants defined
defined('YII_MODE') or define('YII_MODE', 'production');
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 0);

// define current host
defined('VIRA_HOST') or define('VIRA_HOST', $_SERVER['HTTP_HOST']);

// Attaching framework file
require_once(implode(DIRECTORY_SEPARATOR, array(
  dirname(dirname(__FILE__)),
  'framework',
  YII_MODE == 'development' ? 'yii.php' : 'yiilite.php',
)));

// set core alias
Yii::setPathOfAlias('core', implode(DIRECTORY_SEPARATOR, array(
  dirname(dirname(__FILE__)),
  'protected',
  'core',
)));

// import core components
Yii::import('core.*');

// init configuration and start application
VApplicationBootstrap::start(implode(DIRECTORY_SEPARATOR, array(
  dirname(dirname(__FILE__)),
  'protected',
  'config',
)), VApplicationBootstrap::APPLICATION_WEB);
