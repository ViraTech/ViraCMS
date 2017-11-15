<?php
/**
 * ViraCMS Static Page's URL Ruler
 * Based On Yii Framework CBaseUrlRule Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VStaticPageUrlRuler extends VBaseUrlRule
{
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
    if ($route === 'site/page') {
      $url = '';

      if (isset($params['url'])) {
        $url = trim($params['url'], '/');
      }

      if (!empty($url)) {
        $url .= $this->urlSuffix;
      }

      unset($params['url']);

      if (count($params)) {
        foreach ($params as $param => $value) {
          $url .= strpos($url, '?') === false ? '?' : $ampersand;
          $url .= CHtml::encode($param) . '=' . CHtml::encode($value);
        }
      }

      return $url;
    }

    return false;
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
    if ($this->urlSuffix !== null) {
      $pathInfo = $manager->removeUrlSuffix($rawPathInfo, $this->urlSuffix);
    }

    $url = '/' . trim($pathInfo, ' /');

    if (VPage::model()->countByAttributes(array('url' => $url)) > 0) {
      $_GET['url'] = $url;
      return 'site/page';
    }

    return false;
  }
}
