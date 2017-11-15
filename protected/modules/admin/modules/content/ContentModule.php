<?php
/**
 * ViraCMS Site Content Management Module
 *
 * @package vira.core.core
 * @subpackage vira.core.content
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ContentModule extends VSystemWebModule
{
  /**
   * @var string set default controller
   */
  public $defaultController = 'page';

  /**
   * @var string site map widget class name
   */
  public $sitemap;

  /**
   * Get module menu for additional component modules
   * @param VController $ctx current context
   * @return array
   */
  public function getModuleMenu($ctx)
  {
    $menu = array();

    foreach ($this->getModules() as $id => $config) {
      $module = $this->getModule($id);
      if (method_exists($module, 'getModuleMenu')) {
        $menu = CMap::mergeArray($menu, $module->getModuleMenu($ctx));
      }
    }

    return $menu;
  }
}
