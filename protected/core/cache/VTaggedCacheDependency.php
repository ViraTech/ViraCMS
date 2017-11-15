<?php
/**
 * ViraCMS Tagged Cache Component
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VTaggedCacheDependency extends CCacheDependency
{
  /**
   * Cache prefix
   */
  const CACHE_PREFIX = 'Vira.Tagged';

  /**
   * @var string cache component identifier
   */
  public $cacheID = 'cache';

  /**
   * @var string tag name
   */
  private $_tag;

  /**
   * @var integer cache expire duration, zero for none
   */
  private $_duration;

  public function __construct($tag, $duration = 0)
  {
    $this->_tag = $tag;
    $this->_duration = $duration;
  }

  /**
   * Generate and set cache data
   * @return mixed cache data
   */
  public function generateDependentData()
  {
    if (Yii::app()->hasComponent($this->cacheID) && ($cache = Yii::app()->getComponent($this->cacheID)) !== null) {
      if (($data = $cache->get(self::CACHE_PREFIX . $this->_tag)) === false) {
        $data = microtime();
        $cache->set(self::CACHE_PREFIX . $this->_tag, $data, $this->_duration);
      }

      return $data;
    }

    return null;
  }
}
