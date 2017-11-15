<?php
/**
 * ViraCMS Page Accessibility Options Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPageAccessibilityCollection extends CMap
{
  const EVERYONE = 0;
  const AUTHENTICATED_ONLY = 1;
  const GUEST_ONLY = 2;
  const ADMINISTRATOR_ONLY = 3;

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::EVERYONE           => Yii::t('admin.content.collections', 'Allow for everyone'),
      self::AUTHENTICATED_ONLY => Yii::t('admin.content.collections', 'Allow only for authenticated users'),
      self::GUEST_ONLY         => Yii::t('admin.content.collections', 'Allow only for unauthenticated users'),
      self::ADMINISTRATOR_ONLY => Yii::t('admin.content.collections', 'Allow only for site administrators'),
    );

    parent::__construct($data, $readOnly);
  }
}
