<?php
/**
 * ViraCMS Page Visibility Options Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPageVisibilityCollection extends CMap
{
  const VISIBLE = 0;
  const HIDDEN = 1;
  const VISIBLE_AUTHENTICATED = 2;
  const HIDDEN_AUTHENTICATED = 3;

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::VISIBLE               => Yii::t('admin.content.collections', 'Visible for everyone'),
      self::VISIBLE_AUTHENTICATED => Yii::t('admin.content.collections', 'Visible only to authenticated users'),
      self::HIDDEN                => Yii::t('admin.content.collections', 'Hidden from all'),
      self::HIDDEN_AUTHENTICATED  => Yii::t('admin.content.collections', 'Hidden only from authenticated users'),
    );

    parent::__construct($data, $readOnly);
  }
}
