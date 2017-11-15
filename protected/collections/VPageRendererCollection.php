<?php
/**
 * ViraCMS Page Renderer Collection
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class VPageRendererCollection extends CMap
{
  const STATIC_RENDERER = 'VStaticPageRenderer';
  const REDIRECT_RENDERER = 'VRedirectPageRenderer';
  const EXTERNAL_REDIRECT_RENDERER = 'VExternalRedirectRenderer';
  const FORWARD_RENDERER = 'VForwardPageRenderer';

  function __construct($data = null, $readOnly = true)
  {
    $data = array(
      self::STATIC_RENDERER            => Yii::t('admin.content.collections', 'Static Page'),
      self::REDIRECT_RENDERER          => Yii::t('admin.content.collections', 'Redirect To'),
      self::EXTERNAL_REDIRECT_RENDERER => Yii::t('admin.content.collections', 'External Redirect'),
      self::FORWARD_RENDERER           => Yii::t('admin.content.collections', 'Forward To'),
    );

    parent::__construct($data, $readOnly);
  }

  /**
   * Return default renderer
   * @return string
   */
  public function getDefaultRenderer()
  {
    return self::STATIC_RENDERER;
  }

  /**
   * Filter all available renderers by actions
   * @param mixed $action array of actions or single action
   * @return array
   */
  public function getActionRenderer($action)
  {
    if (!is_array($action)) {
      $action = array($action);
    }

    $renderers = array();

    foreach ($this as $key => $value) {
      if (in_array(Yii::app()->collection->rendererAction->getRendererAction($key), $action)) {
        $renderers[] = $key;
      }
    }

    return $renderers;
  }
}
