<?php
/**
 * ViraCMS Account Role Event Handlers
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VAccountRoleEventHandler extends VEventHandler
{
  /**
   * Fired after account role has been removed
   * @param CEvent $event
   */
  public function delete($event)
  {
    VAccountAccess::model()->deleteAllByAttributes(array(
      'accountRoleID' => $event->sender->id,
    ));
  }
}
