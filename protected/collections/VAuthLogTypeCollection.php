<?php
/**
 * ViraCMS Authentication Log Action Types Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VAuthLogTypeCollection extends CMap
{
  const LOGIN = 0;
  const LOGOUT = 1;
  const ACCESS_REQUESTED = 2;
  const ACCESS_GRANTED = 3;

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::LOGIN            => Yii::t('admin.registry.collections', 'Login'),
      self::LOGOUT           => Yii::t('admin.registry.collections', 'Logout'),
      self::ACCESS_REQUESTED => Yii::t('admin.registry.collections', 'Requested Access Restore'),
      self::ACCESS_GRANTED   => Yii::t('admin.registry.collections', 'Access Granted'),
    );

    parent::__construct($data, $readOnly);
  }
}
