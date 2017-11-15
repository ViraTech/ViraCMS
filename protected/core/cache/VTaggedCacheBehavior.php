<?php
/**
 * ViraCMS Tagged Cache Component Behavior
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VTaggedCacheBehavior extends CBehavior
{
  /**
   * Removes cache tag cause all dependent cache records is expired
   * @param string $tag tag name
   * @return boolean
   */
  public function deleteTag($tag)
  {
    return $this->owner->delete(VTaggedCacheDependency::CACHE_PREFIX . $tag);
  }
}
