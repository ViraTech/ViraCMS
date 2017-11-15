<?php
/**
 * ViraCMS Automated Widgets Output Cache
 *
 * @package vira.core.core
 * @subpackage vira.core.widgets
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VAutoCacheWidget extends CWidget
{
  /**
   * @var VWidget the widget
   */
  public $widget;

  /**
   * Get cache key acquired from widget rendering
   * @return string
   */
  public function getCacheKey()
  {
    return $this->widget->getCacheKey();
  }

  /**
   * Form parameters for COuputCache
   * @return array
   */
  public function getCacheParams()
  {
    $params = $this->widget->getCacheParams();

    $params['cacheID'] = $this->widget->getCacheID();

    $dependency = $this->widget->getCacheDependency();
    if (is_a($dependency, 'ICacheDependency')) {
      $params['dependency'] = $dependency;
    }

    return $params;
  }

  /**
   * Execute the widget
   */
  public function run()
  {
    if (is_a($this->widget, 'VWidget')) {
      $this->render('auto-cache-output');
    }
  }
}
