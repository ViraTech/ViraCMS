<?php
/**
 * ViraCMS Block Renderers Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VBlockRendererCollection extends CMap
{
  const STATIC_RENDERER = 'VStaticRenderer';
  const WIDGET_RENDERER = 'VWidgetRenderer';

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::STATIC_RENDERER => Yii::t('admin.content.collections', 'Static Block'),
      self::WIDGET_RENDERER => Yii::t('admin.content.collections', 'Widget'),
    );

    parent::__construct($data, $readOnly);
  }
}
