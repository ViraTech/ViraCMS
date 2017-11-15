<?php
/**
 * ViraCMS Bootstrap Related Components Management Module
 *
 * @package vira.core.core
 * @subpackage vira.core.bootstrap
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class ComponentModule extends VSystemWebModule
{
  /**
   * @var string set default controller
   */
  public $defaultController = 'photo';

  public $publish = true;
}
