<?php
/**
 * ViraCMS Access Rules Permissions Model
 *
 * @package vira.core.core
 * @subpackage vira.core.registry
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 *
 * @property string $accountRoleID account role identifier
 * @property string $accessRuleID access rule identifier
 * @property boolean $permit action is permitted
 */
class VAccountAccess extends VActiveRecord
{
  /**
   * @param string $className
   * @return VAccountAccess
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{core_account_access}}';
  }

  public function primaryKey()
  {
    return array(
      'accountRoleID',
      'accessRuleID',
    );
  }
}
