<?php
/**
 * ViraCMS Page Area Types Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPageAreaTypeCollection extends CMap
{
  const COMMON = 1;
  const PRIMARY = 2;
  const EXTRA = 3;

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::COMMON  => Yii::t('admin.content.collections', 'System'),
      self::PRIMARY => Yii::t('admin.content.collections', 'Primary'),
      self::EXTRA   => Yii::t('admin.content.collections', 'Extra'),
    );

    parent::__construct($data, $readOnly);
  }
}
