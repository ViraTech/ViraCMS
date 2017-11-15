<?php
/**
 * ViraCMS Base Widget Class
 * Based On Yii Framework CWidget Class
 *
 * @package vira.core.core
 * @version 1.0.0
 * @copyright (c) 2015-2017, Vira Technologies http://viratechnologies.ru/
 * @author Eugene V Chernyshev <dev@vira-tech.ru>
 * @license http://market.viracms.ru/legal/ Vira Technologies License Agreements
 * @link https://github.com/ViraTech/ViraCMS ViraCMS community revision git repository
 */
abstract class VWidget extends CWidget
{
  const DEFAULT_CACHE_DURATION = 3600;

  /**
   * @var string widget base path cache
   */
  protected $_basePath;

  /**
   * @var string widget base alias cache
   */
  protected $_baseAlias;

  /**
   * @var boolean cache output of the widget
   */
  public $cacheEnabled = true;

  /**
   * @var integer the counter for registered scripts
   */
  protected $_scriptCounter = 0;

  /**
   * Return widget base alias
   * @return string
   */
  public function getBaseAlias()
  {
    if (empty($this->_baseAlias)) {
      $widget = Yii::app()->widgetFactory->getWidgetByClassName(get_class($this));
      $this->_baseAlias = $widget['baseAlias'];
    }

    return $this->_baseAlias;
  }

  /**
   * Return widget base path
   * @return string
   */
  public function getBasePath()
  {
    if (empty($this->_basePath)) {
      $reflection = new ReflectionClass(get_class($this));
      $this->_basePath = dirname($reflection->getFileName());
    }

    return $this->_basePath;
  }

  /**
   * Return name of the cache component, default is 'cache'
   * @return string
   */
  public function getCacheID()
  {
    return 'cache';
  }

  /**
   * Must return cache key or false
   * @return string
   */
  public function getCacheKey()
  {
    return get_class($this);
  }

  /**
   * Must return cache duration in seconds
   */
  public function getDuration()
  {
    return YII_DEBUG ? 1 : self::DEFAULT_CACHE_DURATION;
  }

  /**
   * Must return cache parameters suitable for @see COutputCache
   * @return array
   */
  public function getCacheParams()
  {
    return array();
  }

  /**
   * Must return cache dependency object of false if no dependency is defined
   * @return mixed cache dependency
   */
  public function getCacheDependency()
  {
    return false;
  }

  /**
   * Register widget javascript
   * @param string $script the javascript code
   * @param integer $position the script position (@see CClientScript)
   * @param array $htmlOptions the HTML tag options
   */
  public function registerScript($script, $position = CClientScript::POS_READY, $htmlOptions = array())
  {
    if ($this->beforeRegisterScript($script, $position, $htmlOptions)) {
      if (isset($htmlOptions['id'])) {
        $id = $htmlOptions['id'];
        unset($htmlOptions['id']);
      }
      else {
        $id = $this->id . '#' . ++$this->_scriptCounter;
      }

      Yii::app()->getClientScript()->registerScript($id, $script, $position, $htmlOptions);
    }
  }

  /**
   * Runs right before script would be registered.
   * You can prevent this action return false, also as modify all of params.
   * @param string $script the javascript code
   * @param integer $position the script position (@see CClientScript)
   * @param array $htmlOptions the HTML tag options
   * @return boolean
   */
  protected function beforeRegisterScript(&$script, &$position, &$htmlOptions)
  {
    return true;
  }

  /**
   * Register widget javascript file
   * @param string $url the javascript file URL
   * @param integer $position the script tag position
   * @param array $htmlOptions the HTML tag options
   */
  public function registerScriptFile($url, $position = null, $htmlOptions = array())
  {
    if ($this->beforeRegisterScriptFile($url, $position, $htmlOptions)) {
      Yii::app()->getClientScript()->registerScriptFile($url, $position, $htmlOptions);
    }
  }

  /**
   * Runs right before script file would be added.
   * You can prevent this action return false, also as modify all of params.
   * @param string $url the javascript file URL
   * @param integer $position the script tag position
   * @param array $htmlOptions the HTML tag options
   * @return boolean
   */
  protected function beforeRegisterScriptFile(&$url, &$position, &$htmlOptions)
  {
    return true;
  }

  /**
   * Must return widget configuration parameters form
   */
  abstract public function getParamsModel();

  /**
   * Must return widget configuration view
   */
  abstract public function getConfigView();
}
