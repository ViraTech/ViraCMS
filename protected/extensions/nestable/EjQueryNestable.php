<?php
/**
 * Wrapper for Nestable jQuery Plugin (http://dbushell.com/)
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
class EjQueryNestable extends CWidget
{
  /**
   * @var array plugin default settings
   */
  private $_defaultSettings = array(
    'listNodeName' => 'ul',
    'itemNodeName' => 'li',
    'labelNodeName' => 'a',
    'rootClass' => 'sitemap',
    'dragClass' => 'sitemap-dragging',
    'placeClass' => 'sitemap-placeholder',
    'dataDisabledItem' => 'data-disabled-item',
    'dataFirstInClass' => 'data-homepage',
    'dataNoChild' => 'data-no-child',
    'emptyClass' => 'sitemap-empty-item',
    'maxDepth' => 30,
    'threshold' => 5,
    'onDragStart' => 'js:function() {}',
    'onDragStop' => 'js:function() {}',
  );

  /**
   * @var string URL of published assets
   */
  private $assetsUrl;

  /**
   * @var CClientScript
   */
  private $cs;

  /**
   * @var string jQuery selector
   */
  public $selector = '';

  /**
   * @var array plugin settings
   */
  public $settings = array();

  /**
   * @var integer Script position as it's accepted by CClientScript::registerScript
   */
  public $scriptPosition;

  public function init()
  {
    $this->assetsUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
    $this->cs = Yii::app()->getClientScript();
    $this->cs->registerCoreScript('jquery');
    $this->cs->registerScriptFile($this->assetsUrl . '/js/jquery.nestable' . (YII_DEBUG ? '' : '.min') . '.js', $this->scriptPosition ? $this->scriptPosition : CClientScript::POS_HEAD);
    $this->cs->registerCssFile($this->assetsUrl . '/css/jquery.nestable.css');
  }

  public function run()
  {
    $options = array();

    foreach ($this->settings as $key => $value) {
      if ($this->_defaultSettings[$key] != $value) {
        $options[$key] = $value;
      }
    }

    $options = empty($options) ? '' : CJavaScript::encode($options);

    $this->cs->registerScript(get_class($this) . '#' . $this->id, "jQuery('{$this->selector}').nestable({$options});", $this->scriptPosition ? $this->scriptPosition : CClientScript::POS_HEAD);
  }
}
