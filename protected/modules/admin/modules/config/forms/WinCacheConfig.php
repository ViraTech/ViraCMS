<?php
/**
 * ViraCMS Windows Cache Component Configuration Form
 *
 * @package vira.core.core
 * @subpackage vira.core.admin
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class WinCacheConfig extends BaseCacheConfig
{
  public $class = 'CWinCache';

  public function getFormAttributes()
  {
    return array();
  }

  public function getConfiguration()
  {
    return array(
      'class' => $this->class,
    );
  }
}
