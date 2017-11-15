<?php
/**
 * ViraCMS History Events Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VHistoryEventCollection extends CMap
{
  const CREATED = 'created';
  const UPDATED = 'updated';
  const PUBLISHED = 'published';
  const HIDDEN = 'hidden';

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::CREATED   => Yii::t('admin.content.collections', 'The Record Created'),
      self::UPDATED   => Yii::t('admin.content.collections', 'The Record Updated'),
      self::PUBLISHED => Yii::t('admin.content.collections', 'The Record Published'),
      self::HIDDEN    => Yii::t('admin.content.collections', 'The Record Hidden'),
    );

    parent::__construct($data, $readOnly);
  }
}
