<?php
/**
 * ViraCMS Page Renderer Actions Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VRendererActionCollection extends CMap
{
  const ACTION_OUTPUT = 'output';
  const ACTION_REDIRECT = 'redirect';
  const ACTION_EXTERNAL_REDIRECT = 'external-redirect';

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::ACTION_OUTPUT            => Yii::t('admin.content.collections', 'Output Renderer'),
      self::ACTION_REDIRECT          => Yii::t('admin.content.collections', 'Redirect Renderer'),
      self::ACTION_EXTERNAL_REDIRECT => Yii::t('admin.content.collections', 'External Redirect Renderer'),
    );

    parent::__construct($data, $readOnly);
  }

  /**
   * Return actions suitable for specified renderer
   * @param string $renderer renderer class
   * @return string
   */
  public function getRendererAction($renderer)
  {
    switch ($renderer) {
      case VPageRendererCollection::STATIC_RENDERER:
        return self::ACTION_OUTPUT;

      case VPageRendererCollection::FORWARD_RENDERER:
      case VPageRendererCollection::REDIRECT_RENDERER:
        return self::ACTION_REDIRECT;

      case VPageRendererCollection::EXTERNAL_REDIRECT_RENDERER:
        return self::ACTION_EXTERNAL_REDIRECT;
    }
  }
}
