<?php
/**
 * ViraCMS Base URL Rule For URL Manager
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VBaseUrlRule extends CBaseUrlRule
{
  /**
   * @var string generated URL's suffix
   */
  public $urlSuffix;

  /**
   * @var array static rules cache
   */
  protected $_staticRules = array();

  /**
   * Initialization of application component
   */
  function __construct()
  {
    if ($this->urlSuffix === null) {
      $this->urlSuffix = Yii::app()->urlManager->urlSuffix;
    }
    $static = $this->getStaticRules();
    foreach ($static as $pattern => $route) {
      $this->_staticRules[] = new CUrlRule($route, $pattern);
    }
  }

  /**
   * Return static rules
   * @return array
   */
  protected function getStaticRules()
  {
    return array();
  }

  /**
   * Creates an URL
   * @param VUrlManager $manager the URL manager
   * @param string $route the route
   * @param array $params list of parameters (name => value) associated with the route
   * @param string $ampersand the token separating name-value pairs in the URL.
   * @return mixed the constructed URL. False if this rule does not apply.
   */
  public function createUrl($manager, $route, $params, $ampersand)
  {
    $url = false;

    foreach ($this->_staticRules as $rule) {
      if (($url = $rule->createUrl($manager, $route, $params, $ampersand)) !== false) {
        break;
      }
    }

    return $url;
  }

  /**
   * Parses an URL
   * @param VUrlManager $manager the URL manager
   * @param CHttpRequest $request the request object
   * @param string $pathInfo path info part of the URL (URL suffix is already removed)
   * @param string $rawPathInfo path info that contains the potential URL suffix
   * @return mixed the route that consists of the controller ID and action ID. False if this rule does not apply.
   */
  public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
  {
    $route = false;

    foreach ($this->_staticRules as $rule) {
      if (($route = $rule->parseUrl($manager, $request, $pathInfo, $rawPathInfo)) !== false) {
        break;
      }
    }

    return $route;
  }
}
