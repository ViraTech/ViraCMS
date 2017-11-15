<?php
/**
 * ViraCMS Default Backend Web Module Component
 * Based On Yii Framework CWebModule Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VSystemWebModule extends VWebModule
{
  /**
   * @var string module custom backend theme name
   */
  public $theme = false;

  /**
   * Module init
   */
  public function init()
  {
    parent::init();
    // set error handler route
    Yii::app()->errorHandler->errorAction = '/admin/error/error';

    // set user login route
    Yii::app()->user->setLoginUrl(array('/admin/auth/login'));

    // set selected backend theme if any
    if ($this->theme !== false) {
      Yii::app()->setTheme($this->theme, VThemeManager::THEME_BACKEND);
    }
  }

  /**
   * Return module contents menu
   * @param VController $ctx controller as context
   * @return array
   */
  public function getModuleMenu($ctx)
  {
    return array();
  }

  /**
   * Return default module entry point as action route
   * @return string
   */
  public function getDefaultEntryPoint()
  {
    return '/' . $this->id . '/' . $this->defaultController . '/';
  }

  /**
   * Return entry points for redirect/forward functionality
   * @return array
   */
  public function getEntryPoints()
  {
    return array();
  }

  /**
   * Return available items for selected entry point
   * @param array $entry array held entry point as controller and optionally action, controller always goes first
   * @param string $siteID site identifier
   * @return array
   */
  public function getAvailableItems($entry, $siteID)
  {
    return array();
  }
}
