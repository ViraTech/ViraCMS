<?php
/**
 * ViraCMS Page Area Containers Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPageAreaContainerCollection extends CMap
{
  const FIXED = 'container';
  const FLUID = 'container-fluid';

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::FIXED => Yii::t('admin.content.collections', 'Responsive (fixed)'),
      self::FLUID => Yii::t('admin.content.collections', 'Fluid (full width)'),
    );

    parent::__construct($data, $readOnly);
  }
}
