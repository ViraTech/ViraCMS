<?php
/**
 * ViraCMS User Types Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VAccountTypeCollection extends CMap
{
  // account types
  const GUEST = 0;
  const USER = 1;
  const ADMINISTRATOR = 2;

  // list of user statuses
  const STATUS_USER_UNVERIFIED = 0;
  const STATUS_USER_ACTIVE = 1;
  const STATUS_USER_BANNED = 2;

  // list of administrator statuses
  const STATUS_ADMINISTRATOR_ACTIVE = 1;
  const STATUS_ADMINISTRATOR_DISABLED = 2;

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::GUEST         => Yii::t('admin.registry.collections', 'Guest'),
      self::USER          => Yii::t('admin.registry.collections', 'Registered User'),
      self::ADMINISTRATOR => Yii::t('admin.registry.collections', 'Site Administrator'),
    );

    parent::__construct($data, $readOnly);
  }

  /**
   * Return account status list or title
   * @param integer $type account type (user or administrator)
   * @param integer $status (optional) account status
   * @return mixed
   */
  public function getAccountStatus($type, $status = null)
  {
    switch ($type) {
      case self::USER:
        $statuses = array(
          self::STATUS_USER_UNVERIFIED => Yii::t('admin.registry.collections', 'Unverified'),
          self::STATUS_USER_ACTIVE     => Yii::t('admin.registry.collections', 'Active'),
          self::STATUS_USER_BANNED     => Yii::t('admin.registry.collections', 'Banned'),
        );
        break;

      case self::ADMINISTRATOR:
        $statuses = array(
          self::STATUS_ADMINISTRATOR_ACTIVE   => Yii::t('admin.registry.collections', 'Active'),
          self::STATUS_ADMINISTRATOR_DISABLED => Yii::t('admin.registry.collections', 'Disabled'),
        );
    }

    return isset($statuses[ $status ]) ? $statuses[ $status ] : $statuses;
  }
}
